<?php

namespace App\Controller;

use App\Entity\TimeEntry;
use App\Form\TimeEntryType;
use App\Repository\TimeEntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TimeEntryController extends AbstractController
{
    #[Route('/my-time-entries', name: 'my_time_entries')]
    public function myEntries(TimeEntryRepository $repo): Response
    {
        $user = $this->getUser();
        $entries = $repo->findBy(['user' => $user]);
        return $this->render('time_entries/list.html.twig', ['entries' => $entries]);
    }

    #[Route('/time-entries', name: 'time_entry_list')]
    public function allEntries(TimeEntryRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $entries = $repo->findAll();
        return $this->render('time_entries/list.html.twig', ['entries' => $entries]);
    }

    #[Route('/time-entries/new', name: 'time_entry_new')]
    public function new(Request $request, EntityManagerInterface $em): Response {
        $entry = new TimeEntry();
        $entry->setUser($this->getUser()); // Besitzer setzen

        $form = $this->createForm(TimeEntryType::class, $entry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($entry);
            $em->flush();
            $this->addFlash('success', 'Zeiteintrag angelegt.');
            return $this->redirectToRoute('time_entry_list');
        }

        return $this->render('time_entries/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
