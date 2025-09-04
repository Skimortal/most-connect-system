<?php
namespace App\Controller;

use App\Entity\EmailTemplate;
use App\Form\EmailTemplateType;
use App\Repository\EmailTemplateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/email_template')]
class EmailTemplateController extends AbstractController {

    #[Route(name: 'email_template_list', methods: ['GET'])]
    public function index(EmailTemplateRepository $emailTemplateRepository): Response
    {
        $currentUser = $this->getUser();

        // Zugriff verweigern für normale Benutzer
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_SUPERUSER')) {
            throw $this->createAccessDeniedException('Kein Zugriff auf die Benutzerliste.');
        }

        // Superadmin sieht alle
        if ($this->isGranted('ROLE_SUPERUSER')) {
            $emailTemplates = $emailTemplateRepository->findAll();
        }
        else {
            // Fallback – sollte nie erreicht werden
            $emailTemplates = [];
        }

        return $this->render('email_template/index.html.twig', [
            'email_templates' => $emailTemplates,
        ]);
    }

    #[Route('/{id}/edit', name: 'email_template_edit')]
    public function edit(Request $request, EmailTemplate $emailTemplate, EntityManagerInterface $entityManager, TranslatorInterface $t): Response
    {
        $form = $this->createForm(EmailTemplateType::class, $emailTemplate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($emailTemplate);
                $entityManager->flush();

                $this->addFlash('success', $t->trans('data_saved_success'));
                return $this->redirectToRoute('email_template_edit', ['id' => $emailTemplate->getId()]);
            } catch (\Throwable $e) {
                $this->addFlash('danger', $t->trans('data_save_error'));
            }
        }

        return $this->render('email_template/detail.html.twig', [
            'email_template' => $emailTemplate,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'email_template_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, TranslatorInterface $t): Response
    {
        $emailTemplate = new EmailTemplate();
        $form = $this->createForm(EmailTemplateType::class, $emailTemplate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($emailTemplate);
                $entityManager->flush();

                $this->addFlash('success', $t->trans('data_saved_success'));
                return $this->redirectToRoute('email_template_list', [], Response::HTTP_SEE_OTHER);
            } catch (\Throwable $e) {
                $this->addFlash('danger', $t->trans('data_save_error'));
            }
        }

        return $this->render('email_template/detail.html.twig', [
            'email_template' => $emailTemplate,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'email_template_delete')]
    public function delete(Request $request, EmailTemplate $emailTemplate, EntityManagerInterface $entityManager, TranslatorInterface $t): Response
    {
        try {
            $entityManager->remove($emailTemplate);
            $entityManager->flush();
            $this->addFlash('warning', $t->trans('data_deleted_success'));
            return $this->redirectToRoute('email_template_list', [], Response::HTTP_SEE_OTHER);
        } catch (\Throwable $e) {
            $this->addFlash('danger', $t->trans('data_save_error').": ".$e->getMessage());
            return $this->redirectToRoute('email_template_edit', ['id' => $emailTemplate->getId()]);
        }
    }

}
