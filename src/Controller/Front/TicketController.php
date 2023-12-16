<?php

namespace App\Controller\Front;

use App\Entity\Comment;
use App\Entity\Ticket;
use App\Form\CommentFormType;
use App\Form\TicketFormType;
use App\Repository\CommentRepository;
use App\Repository\StatutTicketRepository;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;
use App\Service\Point;
use Ramsey\Uuid\Uuid;
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
        $tickets = $ticketRepository->findBy([],['updated_at'=>'DESC']);
        return $this->render('front/ticket/index.html.twig', [
            'tickets' => $tickets,
        ]);
    }

    #[Route('/new', name: 'app_ticket_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TicketRepository $ticketRepository, StatutTicketRepository $statutTicketRepository,
                        Point $pointService): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketFormType::class, $ticket);
        $form->remove('author');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setUuid(Uuid::uuid4()->toString());
            $ticket->setCreatedAt(new \DateTimeImmutable());
            $ticket->setUpdatedAt(new \DateTimeImmutable());
            $ticket->setStatut($statutTicketRepository->find(1));
            $ticket->setAuthor($this->getUser());

            $ticketRepository->add($ticket);
            $pointService->addPoint($ticket->getAuthor());

            return $this->redirectToRoute('app_ticket', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('front/ticket/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{uuid}', name: 'app_ticket_show')]
    public function show(Request $request, Ticket $ticket, CommentRepository $commentRepository, Point $pointService): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser());
            $comment->setTicket($ticket);
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setUpdatedAt(new \DateTime());

            $commentRepository->add($comment);
            $pointService->addPoint($ticket->getAuthor());

            $this->addFlash('success', 'Commentaire ajouté avec succès');
            return $this->redirectToRoute('app_ticket_show', ['uuid'=>$ticket->getUuid()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('front/ticket/show.html.twig', [
            'ticket' => $ticket,
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }
}
