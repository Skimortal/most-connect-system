<?php
namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\InvoiceItem;
use App\Form\InvoiceType;
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
    public function index(InvoiceRepository $invoiceRepository): Response
    {
        $currentUser = $this->getUser();

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
            $company = $currentUser->getCompany();
            $invoices = $invoiceRepository->findBy(['company' => $company]);
        }
        else {
            // Fallback – sollte nie erreicht werden
            $invoices = [];
        }

        return $this->render('invoice/index.html.twig', [
            'invoices' => $invoices,
        ]);
    }

    #[Route('/{id}/edit', name: 'invoice_edit')]
    public function edit(Request $request, Invoice $invoice, EntityManagerInterface $entityManager, TranslatorInterface $t): Response
    {
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                /** @var InvoiceItem $item */
                foreach ($invoice->getInvoiceItems() as $item) {
                    $item->setTotal($item->calcLineTotal());
                }
                $invoice->setTotal($invoice->calcInvoiceTotal());
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
        ]);
    }

    #[Route('/new', name: 'invoice_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, TranslatorInterface $t): Response
    {
        $invoice = new Invoice();
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
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
        ]);
    }

    #[Route('/{id}', name: 'invoice_delete')]
    public function delete(Request $request, Invoice $invoice, EntityManagerInterface $entityManager, TranslatorInterface $t): Response
    {
        $entityManager->remove($invoice);
        $entityManager->flush();
        $this->addFlash('warning', $t->trans('data_deleted_success'));
        return $this->redirectToRoute('invoice_list', [], Response::HTTP_SEE_OTHER);
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
