<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController {

    #[Route('/users', name: 'user_list')]
    public function index(UserRepository $userRepository): Response
    {
        $currentUser = $this->getUser();

        // Zugriff verweigern für normale Benutzer
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_SUPERADMIN')) {
            throw $this->createAccessDeniedException('Kein Zugriff auf die Benutzerliste.');
        }

        // Superadmin sieht alle
        if ($this->isGranted('ROLE_SUPERADMIN')) {
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

    #[Route('/user/{id}/edit', name: 'user_edit')]
    public function edit(User $user, Request $request, EntityManagerInterface $em): Response
    {
        // Optional: Zugriffsbeschränkung
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_SUPERADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Benutzer aktualisiert.');
            return $this->redirectToRoute('user_edit', ['id' => $user->getId()]);
        }

//        dump($form->createView());
//        die();

        return $this->render('user/detail.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

}
