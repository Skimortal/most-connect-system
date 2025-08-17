<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Company;
use App\Entity\User;
use App\Form\AddressType;
use App\Form\CompanyTypeFormType;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/company')]
final class CompanyController extends AbstractController
{
    #[Route(name: 'company_list', methods: ['GET'])]
    public function index(CompanyRepository $companyRepository): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        // Zugriff verweigern für normale Benutzer
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_SUPERUSER')) {
            throw $this->createAccessDeniedException('Kein Zugriff auf die Benutzerliste.');
        }

        // Superadmin sieht alle
        if ($this->isGranted('ROLE_SUPERUSER')) {
            $companies = $companyRepository->findAll();
        }
        // Admin sieht nur Benutzer in derselben Company
        elseif ($this->isGranted('ROLE_ADMIN')) {
            $company = $currentUser->getCompany();
            $companies = $companyRepository->findBy(['id' => $company->getId()]);
        }
        else {
            $companies = [];
        }

        return $this->render('company/index.html.twig', [
            'companies' => $companies,
        ]);
    }

    #[Route('/{id}/edit', name: 'company_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Company $company, EntityManagerInterface $entityManager, TranslatorInterface $t): Response
    {
        $form = $this->createForm(CompanyTypeFormType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($company);
                $entityManager->flush();

                $this->addFlash('success', $t->trans('data_saved_success'));
                return $this->redirectToRoute('company_edit', ['id' => $company->getId()]);
            } catch (\Throwable $e) {
                $this->addFlash('danger', $t->trans('data_save_error'));
            }
        }

        return $this->render('company/detail.html.twig', [
            'company' => $company,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'company_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, TranslatorInterface $t): Response
    {
        $company = new Company();
        $form = $this->createForm(CompanyTypeFormType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($company);
                $entityManager->flush();

                $this->addFlash('success', $t->trans('data_saved_success'));
                return $this->redirectToRoute('company_list', [], Response::HTTP_SEE_OTHER);
            } catch (\Throwable $e) {
                $this->addFlash('danger', $t->trans('data_save_error'));
            }
        }

        return $this->render('company/detail.html.twig', [
            'company' => $company,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'company_delete')]
    public function delete(Request $request, Company $company, EntityManagerInterface $entityManager, TranslatorInterface $t): Response
    {
        try {
            $entityManager->remove($company);
            $entityManager->flush();
            $this->addFlash('warning', $t->trans('data_deleted_success'));
            return $this->redirectToRoute('company_list', [], Response::HTTP_SEE_OTHER);
        } catch (\Throwable $e) {
            $this->addFlash('danger', $t->trans('data_save_error').": ".$e->getMessage());
            return $this->redirectToRoute('company_edit', ['id' => $company->getId()]);
        }
    }

    #[Route('/{id}/address/new', name: 'company_address_new')]
    public function newAddress(Request $request, Company $company, EntityManagerInterface $entityManager, TranslatorInterface $t): Response
    {
        $address = new Address();
        // Die Adresse direkt mit der Firma verknüpfen
        $address->setCompany($company);

        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($address);
                $entityManager->flush();

                $this->addFlash('success', $t->trans('data_saved_success'));
                return $this->redirectToRoute('company_edit', ['id' => $company->getId()]);
            } catch (\Throwable $e) {
                $this->addFlash('danger', $t->trans('data_save_error'));
            }
        }

        return $this->render('address/detail.html.twig', [
            'form' => $form->createView(),
            'company' => $company,
            'address' => $address,
        ]);
    }

    #[Route('/address/{id}', name: 'company_address_edit')]
    public function editAddress(Request $request, Address $address, EntityManagerInterface $entityManager, TranslatorInterface $t): Response
    {
        $company = $address->getCompany();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($address);
                $entityManager->flush();

                $this->addFlash('success', $t->trans('data_saved_success'));
                return $this->redirectToRoute('company_edit', ['id' => $company->getId()]);
            } catch (\Throwable $e) {
                $this->addFlash('danger', $t->trans('data_save_error'));
            }
        }

        return $this->render('address/detail.html.twig', [
            'form' => $form->createView(),
            'company' => $company,
            'address' => $address,
        ]);
    }

    #[Route('/address/delete/{id}', name: 'company_address_delete')]
    public function deleteAddress(Request $request, Address $address, EntityManagerInterface $entityManager, TranslatorInterface $t): Response
    {
        try {
            $company = $address->getCompany();
            if($company) {
                $company->removeAddress($address);
            }
            $entityManager->remove($address);
            $entityManager->flush();
            $this->addFlash('warning', $t->trans('data_deleted_success'));
            return $this->redirectToRoute('company_edit', ['id' => $company->getId()]);
        } catch (\Throwable $e) {
            $this->addFlash('danger', $t->trans('data_save_error').": ".$e->getMessage());
            return $this->redirectToRoute('company_address_edit', ['id' => $address->getId()]);
        }
    }
}
