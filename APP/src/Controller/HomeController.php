<?php
namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\User;
use App\Enum\InvoiceStatus;
use App\Repository\InvoiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController {

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        if(!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard(InvoiceRepository $invoiceRepository): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $company = $currentUser->getCompany();

        $filterOpen = [];
        $filterSend = [];
        $filterPayed = [];
        if($currentUser && $company) {
            $filterOpen['company'] = $company;
            $filterSend['company'] = $company;
            $filterPayed['company'] = $company;
        }
        $filterOpen['status'] = InvoiceStatus::OFFEN;
        $filterSend['status'] = InvoiceStatus::VERSENDET;
        $filterPayed['status'] = InvoiceStatus::BEZAHLT;

        $invoicesOpen = new ArrayCollection($invoiceRepository->findBy($filterOpen));
        $invoicesSent = new ArrayCollection($invoiceRepository->findBy($filterSend));
        $invoicesPayed = new ArrayCollection($invoiceRepository->findBy($filterPayed));

        return $this->render('home/dashboard.html.twig', [
            'user' => $this->getUser(),
            'invoicesOpen' => $invoicesOpen->count(),
            'invoicesSent' => $invoicesSent->count(),
            'invoicesPayed' => $invoicesPayed->count(),

            'invoicesOpenSum' => array_sum($invoicesOpen->map(fn(Invoice $i) => $i->getTotal())->toArray()),
            'invoicesSentSum' => array_sum($invoicesSent->map(fn(Invoice $i) => $i->getTotal())->toArray()),
            'invoicesPayedSum' => array_sum($invoicesPayed->map(fn(Invoice $i) => $i->getTotal())->toArray()),
        ]);
    }
}
