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
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/ticket')]
class TicketController extends AbstractController
{
    #[Route('/', name: 'app_ticket', methods: ['GET'])]
    public function index(TicketRepository $ticketRepository): Response
    {
        $tickets = $ticketRepository->findBy([],['updated_at'=>'DESC']);
        return $this->render('front/ticket/index.html.twig', [
            'tickets' => $tickets,
        ]);
    }

    #[Route('/new', name: 'app_ticket_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TicketRepository $ticketRepository, StatutTicketRepository $statutTicketRepository,
                        Point $pointService, Security $security): Response
    {
        if (!($security->getUser())) {
            throw new AccessDeniedException('Access Denied: You are not allowed to access this resource.');
        }
        $ticket = new Ticket();
        $form = $this->createForm(TicketFormType::class, $ticket);
        $form->remove('author');
        $form->remove('statut');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setUuid(Uuid::uuid4()->toString());
            $ticket->setCreatedAt(new \DateTimeImmutable());
            $ticket->setUpdatedAt(new \DateTimeImmutable());
            $ticket->setStatut($statutTicketRepository->find(1));
            $ticket->setAuthor($this->getUser());

            $ticketRepository->add($ticket);

            $isNotified = $pointService->addPoint($ticket->getAuthor());
            if($isNotified){
                $this->addFlash('notification', [
                    'Votre ticket a été ajouté avec succès',
                    'Vous venez de passer au niveau suivant ! Félicitation 🤙 </br> Vous êtes maintenant un <span class="font-bungee">'.$ticket->getAuthor()->getLevel()->getName().'</span>'
                ]);
            }
            else{
                $this->addFlash('notification', ['Votre ticket a été ajouté avec succès']);
            }
            return $this->redirectToRoute('app_ticket', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('front/ticket/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{uuid}/comment/edit/{id}', name: 'app_ticket_comment_edit', requirements: ['uuid' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}', 'id' => '\d+'], methods: ['GET', 'POST'])]
    public function editComment(Request $request, $uuid, $id, TicketRepository $ticketRepository, StatutTicketRepository $statutTicketRepository,
                         Point $pointService, CommentRepository $commentRepository, Security $security): Response
    {
        $ticket = $ticketRepository->findOneBy(['uuid'=>$uuid]);
        $comment = $commentRepository->find($id);

        if ($comment->getAuthor()->getUsername() !== $security->getUser()->getUsername()) {
            throw new AccessDeniedException('Access Denied: You are not allowed to access this resource.');
        }

        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUpdatedAt(new \DateTime());
            $commentRepository->add($comment);
            $this->addFlash('notification', ['Votre commentaire a été modifié avec succès']);
            return $this->redirectToRoute('app_ticket_show', ['uuid'=>$ticket->getUuid()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('front/ticket/comment/edit.html.twig', [
            'ticket' => $ticket,
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{uuid}/comment/delete/{id}', name: 'app_ticket_comment_delete', requirements: ['uuid' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}', 'id' => '\d+'], methods: ['POST'])]
    public function deleteComment(Request $request, $uuid, $id, TicketRepository $ticketRepository, CommentRepository $commentRepository, Security $security): Response
    {
        $ticket = $ticketRepository->findOneBy(['uuid'=>$uuid]);
        $comment = $commentRepository->find($id);

        if ($ticket->getAuthor()->getUsername() !== $security->getUser()->getUsername()) {
            throw new AccessDeniedException('Access Denied: You are not allowed to access this resource.');
        }

        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $commentRepository->remove($comment);
            $this->addFlash('notification', ['Commentaire supprimé avec succès']);
        }
        return $this->redirectToRoute('app_ticket_show', ['uuid'=>$uuid], Response::HTTP_SEE_OTHER);
    }

    #[Route('/edit/{uuid}', name: 'app_ticket_edit', requirements: ['uuid' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Ticket $ticket, TicketRepository $ticketRepository, Security $security): Response
    {
        if ($ticket->getAuthor()->getUsername() !== $security->getUser()->getUsername()) {
            throw new AccessDeniedException('Access Denied: You are not allowed to access this resource.');
        }

        $form = $this->createForm(TicketFormType::class, $ticket);
        $form->remove('author');
        $form->remove('statut');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setUpdatedAt(new \DateTimeImmutable());
            $ticketRepository->add($ticket);
            $this->addFlash('notification', ['Votre ticket a été modifié avec succès']);
            return $this->redirectToRoute('app_ticket_show', ['uuid'=>$ticket->getUuid()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('front/ticket/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/close/{uuid}', name: 'app_ticket_close', requirements: ['uuid' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}'], methods: ['POST'])]
    public function close(Request $request, Ticket $ticket, TicketRepository $ticketRepository, StatutTicketRepository $statutTicketRepository, Security $security): Response
    {
        if ($ticket->getAuthor()->getUsername() !== $security->getUser()->getUsername()) {
            throw new AccessDeniedException('Access Denied: You are not allowed to access this resource.');
        }

        if ($this->isCsrfTokenValid('close'.$ticket->getId(), $request->request->get('_token'))) {
            $ticket->setStatut($statutTicketRepository->find(2));
            $ticketRepository->add($ticket);
            $this->addFlash('notification', ['Ticket cloturé avec succès']);
        }
        return $this->redirectToRoute('app_ticket_show', ['uuid'=>$ticket->getUuid()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/delete/{uuid}', name: 'app_ticket_delete', requirements: ['uuid' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}'], methods: ['POST'])]
    public function delete(Request $request, Ticket $ticket, TicketRepository $ticketRepository, CommentRepository $commentRepository, Security $security): Response
    {
        if ($ticket->getAuthor()->getUsername() !== $security->getUser()->getUsername()) {
            throw new AccessDeniedException('Access Denied: You are not allowed to access this resource.');
        }

        if ($this->isCsrfTokenValid('delete'.$ticket->getId(), $request->request->get('_token'))) {
            //On supprime les commentaires liés
            $comments = $ticket->getComments();
            foreach ($comments as $comment) {
                $commentRepository->remove($comment);
            }
            $ticketRepository->remove($ticket);
            $this->addFlash('notification', ['Ticket supprimé avec succès']);
        }
        return $this->redirectToRoute('app_ticket', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{uuid}', name: 'app_ticket_show', requirements: ['uuid' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}'], methods: ['GET', 'POST'])]
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

            $this->addFlash('notification', ['Votre commentaire a été ajouté avec succès 👏']);
            return $this->redirectToRoute('app_ticket_show', ['uuid'=>$ticket->getUuid()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('front/ticket/show.html.twig', [
            'ticket' => $ticket,
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }
}
