<?php
namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Invoice;
use App\Entity\User;
use App\Form\InvoiceFilterType;
use App\Form\InvoiceType;
use App\Model\InvoiceFilter;
use App\Repository\CustomerRepository;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

        $allowedCustomers = $customerRepository->findAllForUser($currentUser);

        if ($customer) {
            if (!in_array($customer, $allowedCustomers, true)) {
                throw $this->createAccessDeniedException('Customer not allowed.');
            }
            $invoice->setCustomer($customer);
        }

        $form = $this->createForm(InvoiceType::class, $invoice, [
            'customers' => $allowedCustomers,
            'hide_customer' => (bool) $customer,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $chosen = $invoice->getCustomer();
            if (!in_array($chosen, $allowedCustomers, true)) {
                throw $this->createAccessDeniedException('Customer not allowed.');
            }

            try {
                $invoice->setAllPrices();
                $entityManager->persist($invoice);
                $entityManager->flush();

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

        $allowedCustomers = $customerRepository->findAllForUser($currentUser);

        if ($customer) {
            if (!in_array($customer, $allowedCustomers, true)) {
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
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $chosen = $invoice->getCustomer();
            if (!in_array($chosen, $allowedCustomers, true)) {
                throw $this->createAccessDeniedException('Customer not allowed.');
            }

            try {
                $invoice->setAllPrices();
                $entityManager->persist($invoice);
                $entityManager->flush();

                $this->addFlash('success', $t->trans('data_saved_success'));
                return $this->redirectToRoute('invoice_list', [], Response::HTTP_SEE_OTHER);
            } catch (\Throwable $e) {
                $this->addFlash('danger', $t->trans('data_save_error'));
            }
        }

        return $this->render('invoice/detail.html.twig', [
            'invoice' => $invoice,
            'form' => $form,
            'hide_customer' => (bool) $customer,
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
    public function sendPdf(Invoice $invoice, \Symfony\Component\Mailer\MailerInterface $mailer): Response
    {
        // 1. PDF generieren (z. B. über Dompdf oder TCPDF)
        $pdfContent = $this->renderView('invoice/pdf/design1.html.twig', [
            'invoice' => $invoice,
        ]);

        // Beispiel mit Dompdf:
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($pdfContent);
        $dompdf->render();
        $pdfOutput = $dompdf->output();

        // 2. Mail zusammenstellen
        $email = (new \Symfony\Component\Mime\Email())
            ->from('info@deine-domain.tld')
            ->to($invoice->getCustomer()->getEmail()) // Empfänger aus Customer-Entität
            ->subject('Ihre Rechnung ' . $invoice->getInvoiceNumber())
            ->text('Sehr geehrte/r ' . $invoice->getCustomer() . ', im Anhang finden Sie Ihre Rechnung.')
            ->attach($pdfOutput, 'Rechnung-' . $invoice->getInvoiceNumber() . '.pdf', 'application/pdf');

        // 3. Mail versenden
        $mailer->send($email);

        // 4. Feedback geben
        $this->addFlash('success', 'Die Rechnung wurde an den Kunden gesendet.');

        return $this->redirectToRoute('invoice_edit', ['id' => $invoice->getId()]);
    }

}
