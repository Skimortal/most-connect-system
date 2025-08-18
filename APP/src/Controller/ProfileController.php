<?php
// src/Controller/ProfileController.php
namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class ProfileController extends AbstractController
{
    #[Route('/me', name: 'my_profile', methods: ['GET','POST'])]
    public function edit(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, TranslatorInterface $t): Response {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ProfileType::class, $user, [
            'is_edit' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avatarFile = $form->get('avatarFile')->getData();
            if ($avatarFile) {
                $original = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safe     = $slugger->slug($original);
                $ext      = $avatarFile->guessExtension() ?: 'bin';
                $newName  = $safe.'-'.uniqid().'.'.$ext;

                $avatarFile->move($this->getParameter('avatars_dir'), $newName);
                $user->setAvatarFilename($newName);
            }

            $em->flush();
            $this->addFlash('success', $t->trans('data_saved_success'));
            return $this->redirectToRoute('my_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/my_profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
