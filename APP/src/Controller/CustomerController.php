<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\User;
use App\Form\CustomerType;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/customer')]
final class CustomerController extends AbstractController
{
    #[Route(name: 'customer_list', methods: ['GET'])]
    public function index(CustomerRepository $customerRepository): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        // Zugriff verweigern für normale Benutzer
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_SUPERUSER')) {
            throw $this->createAccessDeniedException('Kein Zugriff auf die Benutzerliste.');
        }

        // Superadmin sieht alle
        if ($this->isGranted('ROLE_SUPERUSER')) {
            $customers = $customerRepository->findAll();
        }
        // Admin sieht nur Benutzer in derselben Company
        elseif ($this->isGranted('ROLE_ADMIN')) {
            $company = $currentUser->getCompany();
            $customers = $customerRepository->findBy(['company' => $company->getId()]);
        }
        else {
            $customers = [];
        }

        return $this->render('customer/index.html.twig', [
            'customers' => $customers,
        ]);
    }

    #[Route('/{id}/edit', name: 'customer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Customer $customer, EntityManagerInterface $entityManager, TranslatorInterface $t): Response
    {
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($customer);
                $entityManager->flush();

                $this->addFlash('success', $t->trans('data_saved_success'));
                return $this->redirectToRoute('customer_edit', ['id' => $customer->getId()]);
            } catch (\Throwable $e) {
                $this->addFlash('danger', $t->trans('data_save_error'));
            }
        }

        return $this->render('customer/detail.html.twig', [
            'customer' => $customer,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'customer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, TranslatorInterface $t): Response
    {
        $customer = new Customer();

        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $hasCompany = (bool) $currentUser?->getCompany();
        if ($hasCompany) {
            $customer->setCompany($currentUser->getCompany());
        }

        $form = $this->createForm(CustomerType::class, $customer, [
            'show_company_field' => !$hasCompany,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($customer);
                $entityManager->flush();

                $this->addFlash('success', $t->trans('data_saved_success'));
                return $this->redirectToRoute('customer_list', [], Response::HTTP_SEE_OTHER);
            } catch (\Throwable $e) {
                dd($e);
                $this->addFlash('danger', $t->trans('data_save_error'));
            }
        }

        return $this->render('customer/detail.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'customer_delete')]
    public function delete(Request $request, Customer $customer, EntityManagerInterface $entityManager, TranslatorInterface $t): Response
    {
        try {
            $entityManager->remove($customer);
            $entityManager->flush();
            $this->addFlash('warning', $t->trans('data_deleted_success'));
            return $this->redirectToRoute('customer_list', [], Response::HTTP_SEE_OTHER);
        } catch (\Throwable $e) {
            $this->addFlash('danger', $t->trans('data_save_error').": ".$e->getMessage());
            return $this->redirectToRoute('customer_edit', ['id' => $customer->getId()]);
        }
    }

}
