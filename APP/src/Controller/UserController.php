<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user')]
class UserController extends AbstractController {

    #[Route(name: 'user_list', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $currentUser = $this->getUser();

        // Zugriff verweigern für normale Benutzer
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_SUPERUSER')) {
            throw $this->createAccessDeniedException('Kein Zugriff auf die Benutzerliste.');
        }

        // Superadmin sieht alle
        if ($this->isGranted('ROLE_SUPERUSER')) {
            $users = $userRepository->findAll();
        }
        // Admin sieht nur Benutzer in derselben Company
        elseif ($this->isGranted('ROLE_ADMIN')) {
            $company = $currentUser->getCompany();
            $users = $userRepository->findBy(['company' => $company]);
        }
        else {
            // Fallback – sollte nie erreicht werden
            $users = [];
        }

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/{id}/edit', name: 'user_edit')]
    public function edit(User $user, Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        // Optional: Zugriffsbeschränkung
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_SUPERUSER')) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(UserType::class, $user, [
            'is_edit' => $user->getId() !== null,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();

            if (!empty($plainPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            $em->flush();

            $this->addFlash('success', 'Benutzer aktualisiert.');
            return $this->redirectToRoute('user_edit', ['id' => $user->getId()]);
        }

        return $this->render('user/detail.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/detail.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'user_delete')]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('user_list', [], Response::HTTP_SEE_OTHER);
    }

}
