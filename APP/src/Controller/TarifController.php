<?php
namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Tarif;
use App\Entity\User;
use App\Enum\EmailTemplateKey;
use App\Enum\TarifDesign;
use App\Enum\TarifStatus;
use App\Form\TarifFilterType;
use App\Form\TarifType;
use App\Model\TarifFilter;
use App\Repository\CustomerRepository;
use App\Repository\TarifRepository;
use App\Service\MailTemplateRenderer;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/tarif')]
class TarifController extends AbstractController {

    #[Route(name: 'tarif_list', methods: ['GET'])]
    public function index(Request $request, TarifRepository $tarifRepository): Response
    {
        $tarifs = $tarifRepository->findAll();
        return $this->render('tarif/index.html.twig', [
            'tarifs' => $tarifs,
        ]);
    }

    #[Route('/{id}/edit', name: 'tarif_edit')]
    public function edit(
        Request $request,
        Tarif $tarif,
        EntityManagerInterface $entityManager,
        TranslatorInterface $t
    ): Response
    {
        $form = $this->createForm(TarifType::class, $tarif);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($tarif);
                $entityManager->flush();

                $this->addFlash('success', $t->trans('data_saved_success'));
                return $this->redirectToRoute('tarif_edit', ['id' => $tarif->getId()]);
            } catch (\Throwable $e) {
                $this->addFlash('danger', $t->trans('data_save_error'));
            }
        }

        return $this->render('tarif/detail.html.twig', [
            'tarif' => $tarif,
            'form' => $form,
        ]);
    }

    #[Route('/new', name: 'tarif_new', methods: ['GET', 'POST'])]
    public function new(
        Request                $request,
        EntityManagerInterface $entityManager,
        TranslatorInterface    $t
    ): Response
    {
        $tarif = new Tarif();
        $form = $this->createForm(TarifType::class, $tarif);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($tarif);
                $entityManager->flush();

                $this->addFlash('success', $t->trans('data_saved_success'));
                return $this->redirectToRoute('tarif_list', [], Response::HTTP_SEE_OTHER);
            } catch (\Throwable $e) {
                $this->addFlash('danger', $t->trans('data_save_error'));
            }
        }

        return $this->render('tarif/detail.html.twig', [
            'tarif' => $tarif,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'tarif_delete')]
    public function delete(Request $request, Tarif $tarif, EntityManagerInterface $entityManager, TranslatorInterface $t): Response
    {
        try {
            $entityManager->remove($tarif);
            $entityManager->flush();
            $this->addFlash('warning', $t->trans('data_deleted_success'));
            return $this->redirectToRoute('tarif_list', [], Response::HTTP_SEE_OTHER);
        } catch (\Throwable $e) {
            $this->addFlash('danger', $t->trans('data_save_error').": ".$e->getMessage());
            return $this->redirectToRoute('tarif_edit', ['id' => $tarif->getId()]);
        }
    }

}
