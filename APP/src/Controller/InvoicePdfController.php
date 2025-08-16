<?php
namespace App\Controller;

use App\Entity\Invoice;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/invoice')]
class InvoicePdfController extends AbstractController
{
    #[Route('/{id}/pdf', name: 'invoice_pdf', methods: ['GET'])]
    public function pdf(Invoice $invoice): Response
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

        // 1) HTML via Twig rendern
        $html = $this->renderView('invoice/pdf/design1.html.twig', [
            'invoice' => $invoice,
            'invoiceAddress' => $invoice->getCompany()->getMainAddress(),
            'logoDataUri' => $logoDataUri,
            'footerLogoDataUri' => $footerLogoDataUri,
            'baseUrl' => $this->getParameter('router.request_context.scheme').'://'.$this->getParameter('router.request_context.host'),
        ]);

        // 2) dompdf konfigurieren
        $options = new Options();
        $options->set('isRemoteEnabled', true);     // remote Assets (Logos/CSS) erlauben
        $options->set('defaultFont', 'DejaVu Sans'); // UTF-8/äöü sicher
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait'); // oder 'landscape'
        $dompdf->render();

        // Optional: Seitenzahlen/Fußzeile
        $canvas = $dompdf->getCanvas();
        $fontMetrics = $dompdf->getFontMetrics();
        $font = $fontMetrics->get_font('DejaVu Sans', 'normal');
        $canvas->page_text(520, 820, "Seite {PAGE_NUM}/{PAGE_COUNT}", $font, 8, [0,0,0]);

        // 3) Inline anzeigen (oder als Download: 'attachment;')
        $filename = sprintf('invoice-%s.pdf', $invoice->getInvoiceNumber() ?? $invoice->getId());
        return new Response($dompdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }
}
