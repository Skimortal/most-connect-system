<?php
namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Invoice;
use App\Entity\User;
use App\Enum\InvoiceDesign;
use App\Enum\InvoiceStatus;
use App\Form\InvoiceFilterType;
use App\Form\InvoiceType;
use App\Model\InvoiceFilter;
use App\Repository\CustomerRepository;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/invoice')]
class InvoiceController extends AbstractController {

    #[Route(name: 'invoice_list', methods: ['GET'])]
    public function index(Request $request, InvoiceRepository $invoiceRepository): Response
    {
        $currentUser = $this->getUser();
        $companyFilter = null;

        // Zugriff verweigern für normale Benutzer
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_SUPERUSER')) {
            throw $this->createAccessDeniedException('Kein Zugriff auf die Benutzerliste.');
        }

        // Superadmin sieht alle
        if ($this->isGranted('ROLE_SUPERUSER')) {
            $invoices = $invoiceRepository->findAll();
        }
        // Admin sieht nur Benutzer in derselben Company
        elseif ($this->isGranted('ROLE_ADMIN')) {
            $companyFilter = $currentUser->getCompany();
        }
        else {
            // Fallback – sollte nie erreicht werden
            $invoices = [];
        }

        $filter = new InvoiceFilter();
        $form = $this->createForm(InvoiceFilterType::class, $filter, ['method' => 'GET']);
        $form->handleRequest($request);

        $invoices = $invoiceRepository->findByFilter($filter, $companyFilter);

        return $this->render('invoice/index.html.twig', [
            'invoices' => $invoices,
            'filterForm' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit/{customer}', name: 'invoice_edit', defaults: ['customer' => null])]
    public function edit(
        Request $request,
        Invoice $invoice,
        EntityManagerInterface $entityManager,
        TranslatorInterface $t,
        ?Customer $customer,
        CustomerRepository $customerRepository): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if($this->isGranted('ROLE_SUPERUSER')) {
            $allowedCustomers = $customerRepository->findAll();
        }
        else {
            $allowedCustomers = $customerRepository->findAllForUser($currentUser);
        }

        if ($customer) {
            if (!$this->isGranted('ROLE_SUPERUSER') && !in_array($customer, $allowedCustomers, true)) {
                throw $this->createAccessDeniedException('Customer not allowed.');
            }
            $invoice->setCustomer($customer);
        }

        $form = $this->createForm(InvoiceType::class, $invoice, [
            'customers' => $allowedCustomers,
            'hide_customer' => (bool) $customer,
            'hide_company' => !$this->isGranted('ROLE_SUPERUSER'),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $chosen = $invoice->getCustomer();
            if (!$this->isGranted('ROLE_SUPERUSER') && !in_array($chosen, $allowedCustomers, true)) {
                throw $this->createAccessDeniedException('Customer not allowed.');
            }

            try {
                $invoice->setAllPrices();
                $entityManager->persist($invoice);
                $entityManager->flush();

                $after = $request->request->get('after_save');
                if ($after === 'pdf') {
                    return $this->redirectToRoute('invoice_pdf', ['id' => $invoice->getId()]);
                }
                if ($after === 'send') {
                    return $this->redirectToRoute('invoice_send_pdf', ['id' => $invoice->getId()]);
                }

                $this->addFlash('success', $t->trans('data_saved_success'));
                return $this->redirectToRoute('invoice_edit', ['id' => $invoice->getId()]);
            } catch (\Throwable $e) {
                $this->addFlash('danger', $t->trans('data_save_error'));
            }
        }

        return $this->render('invoice/detail.html.twig', [
            'invoice' => $invoice,
            'form' => $form,
            'hide_customer' => (bool) $customer,
            'hide_company' => !$this->isGranted('ROLE_SUPERUSER'),
        ]);
    }

    #[Route('/new/{customer}', name: 'invoice_new', defaults: ['customer' => null], methods: ['GET', 'POST'])]
    public function new(
        Request                $request,
        EntityManagerInterface $entityManager,
        TranslatorInterface    $t,
        ?Customer              $customer,
        CustomerRepository     $customerRepository): Response
    {
        $invoice = new Invoice();

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if($this->isGranted('ROLE_SUPERUSER')) {
            $allowedCustomers = $customerRepository->findAll();
        }
        else {
            $allowedCustomers = $customerRepository->findAllForUser($currentUser);
        }

        if ($customer) {
            if (!$this->isGranted('ROLE_SUPERUSER') && !in_array($customer, $allowedCustomers, true)) {
                throw $this->createAccessDeniedException('Customer not allowed.');
            }
            $invoice->setCustomer($customer);
        }

        if($currentUser->getCompany()) {
            $invoice->setCompany($currentUser->getCompany());
        }

        $form = $this->createForm(InvoiceType::class, $invoice, [
            'customers' => $allowedCustomers,
            'hide_customer' => (bool) $customer,
            'hide_company' => !$this->isGranted('ROLE_SUPERUSER'),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $chosen = $invoice->getCustomer();
            if (!$this->isGranted('ROLE_SUPERUSER') && !in_array($chosen, $allowedCustomers, true)) {
                throw $this->createAccessDeniedException('Customer not allowed.');
            }

            try {
                $invoice->setAllPrices();
                $entityManager->persist($invoice);
                $entityManager->flush();

                $this->addFlash('success', $t->trans('data_saved_success'));
                return $this->redirectToRoute('invoice_list', [], Response::HTTP_SEE_OTHER);
            } catch (\Throwable $e) {
                dd($e);
                $this->addFlash('danger', $t->trans('data_save_error'));
            }
        }

        return $this->render('invoice/detail.html.twig', [
            'invoice' => $invoice,
            'form' => $form,
            'hide_customer' => (bool) $customer,
            'hide_company' => !$this->isGranted('ROLE_SUPERUSER'),
        ]);
    }

    #[Route('/{id}', name: 'invoice_delete')]
    public function delete(Request $request, Invoice $invoice, EntityManagerInterface $entityManager, TranslatorInterface $t): Response
    {
        try {
            $entityManager->remove($invoice);
            $entityManager->flush();
            $this->addFlash('warning', $t->trans('data_deleted_success'));
            return $this->redirectToRoute('invoice_list', [], Response::HTTP_SEE_OTHER);
        } catch (\Throwable $e) {
            $this->addFlash('danger', $t->trans('data_save_error').": ".$e->getMessage());
            return $this->redirectToRoute('invoice_edit', ['id' => $invoice->getId()]);
        }
    }

    #[Route('/{id}/send-pdf', name: 'invoice_send_pdf', methods: ['GET'])]
    public function sendPdf(Invoice $invoice, MailerInterface $mailer, EntityManagerInterface $entityManager): Response
    {
        // ------- LOGO ---------
        $logoPath = $this->getParameter('kernel.project_dir')
            . '/public/uploads/company_logos/' . $invoice->getCompany()->getLogoName();
        $logoDataUri = null;
        if (is_file($logoPath)) {
            $mime = mime_content_type($logoPath) ?: 'image/png';
            $logoDataUri = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        // ------- LOGO Footer ---------
        $logoPath = $this->getParameter('kernel.project_dir')
            . '/public/uploads/company_logos/' . $invoice->getCompany()->getLogoSmallName();
        $footerLogoDataUri = null;
        if (is_file($logoPath)) {
            $mime = mime_content_type($logoPath) ?: 'image/png';
            $footerLogoDataUri = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        // 1. PDF generieren (z. B. über Dompdf oder TCPDF)
        $map = [
            InvoiceDesign::MINIMAL->value    => 'minimal.html.twig',
            InvoiceDesign::MODERN->value     => 'modern.html.twig',
            InvoiceDesign::LETTERHEAD->value => 'letterhead.html.twig',
            InvoiceDesign::GRID->value       => 'grid.html.twig',
            InvoiceDesign::ELEGANT->value    => 'elegant.html.twig',
        ];

        $tpl = $map[$invoice->getDesign()->value] ?? $map[InvoiceDesign::MINIMAL->value];
        $pdfContent = $this->renderView('invoice/pdf/'.$tpl, [
            'invoice' => $invoice,
            'invoiceAddress' => $invoice->getCompany()->getMainAddress(),
            'logoDataUri' => $logoDataUri,
            'footerLogoDataUri' => $footerLogoDataUri,
            'baseUrl' => $this->getParameter('router.request_context.scheme').'://'.$this->getParameter('router.request_context.host'),
        ]);

        // Beispiel mit Dompdf:
        $dompdf = new Dompdf();
        $dompdf->loadHtml($pdfContent);
        $dompdf->render();
        $pdfOutput = $dompdf->output();

        // 2. Mail zusammenstellen
        $email = (new Email())
            ->from('no-reply@ineasy.at')
            ->to($invoice->getCustomer()->getEmail())
            ->subject('Ihre Rechnung ' . $invoice->getInvoiceNumber())
            ->text('Sehr geehrte/r ' . $invoice->getCustomer() . ', im Anhang finden Sie Ihre Rechnung.')
            ->attach($pdfOutput, 'Rechnung-' . $invoice->getInvoiceNumber() . '.pdf', 'application/pdf');

        // 3. Mail versenden
        $mailer->send($email);

        if($invoice->getStatus() == InvoiceStatus::OFFEN) {
            $invoice->setStatus(InvoiceStatus::VERSENDET);
            $entityManager->persist($invoice);
            $entityManager->flush();
        }

        // 4. Feedback geben
        $this->addFlash('success', 'Die Rechnung wurde an den Kunden gesendet.');

        return $this->redirectToRoute('invoice_edit', ['id' => $invoice->getId()]);
    }

}
