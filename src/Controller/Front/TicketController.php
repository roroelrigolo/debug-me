<?php

namespace App\Controller\Front;

use App\Entity\Tag;
use App\Entity\Ticket;
use App\Form\TicketFormType;
use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ticket')]
class TicketController extends AbstractController
{
    #[Route('/', name: 'app_ticket')]
    public function index(TicketRepository $ticketRepository): Response
    {
        $tickets = $ticketRepository->findAll();
        return $this->render('front/ticket/index.html.twig', [
            'tickets' => $tickets,
        ]);
    }

    #[Route('/new', name: 'app_ticket_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TicketRepository $ticketRepository): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketFormType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setCreatedAt(new \DateTimeImmutable());
            $ticket->setUpdatedAt(new \DateTimeImmutable());
            $ticket->setAuthor($this->getUser());

            $ticketRepository->add($ticket);

            return $this->redirectToRoute('app_ticket', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('front/ticket/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_ticket_show')]
    public function show($id, TicketRepository $ticketRepository): Response
    {
        $ticket = $ticketRepository->findOneBy(['id'=>$id]);
        return $this->render('front/ticket/show.html.twig', [
            'ticket' => $ticket
        ]);
    }
}
