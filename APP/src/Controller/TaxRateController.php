<?php
namespace App\Controller;

use App\Entity\TaxRate;
use App\Form\TaxRateType;
use App\Repository\TaxRateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tax_rate')]
class TaxRateController extends AbstractController {

    #[Route(name: 'tax_rate_list', methods: ['GET'])]
    public function index(TaxRateRepository $taxRateRepository): Response
    {
        $currentUser = $this->getUser();

        // Zugriff verweigern für normale Benutzer
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_SUPERUSER')) {
            throw $this->createAccessDeniedException('Kein Zugriff auf die Benutzerliste.');
        }

        // Superadmin sieht alle
        if ($this->isGranted('ROLE_SUPERUSER')) {
            $taxRates = $taxRateRepository->findAll();
        }
        // Admin sieht nur Benutzer in derselben Company
        elseif ($this->isGranted('ROLE_ADMIN')) {
            $company = $currentUser->getCompany();
            $taxRates = $taxRateRepository->findBy(['company' => $company]);
        }
        else {
            // Fallback – sollte nie erreicht werden
            $taxRates = [];
        }

        return $this->render('tax_rate/index.html.twig', [
            'tax_rates' => $taxRates,
        ]);
    }

    #[Route('/{id}/edit', name: 'tax_rate_edit')]
    public function edit(Request $request, TaxRate $taxRate, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TaxRateType::class, $taxRate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($taxRate);
            $entityManager->flush();

            return $this->redirectToRoute('tax_rate_edit', ['id' => $taxRate->getId()]);
        }

        return $this->render('tax_rate/detail.html.twig', [
            'tax_rate' => $taxRate,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'tax_rate_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $taxRate = new TaxRate();
        $form = $this->createForm(TaxRateType::class, $taxRate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($taxRate);
            $entityManager->flush();

            return $this->redirectToRoute('tax_rate_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tax_rate/detail.html.twig', [
            'tax_rate' => $taxRate,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'tax_rate_delete')]
    public function delete(Request $request, TaxRate $taxRate, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($taxRate);
        $entityManager->flush();
        return $this->redirectToRoute('tax_rate_list', [], Response::HTTP_SEE_OTHER);
    }

}
