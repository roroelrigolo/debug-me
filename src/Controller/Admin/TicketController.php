<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Entity\Ticket;
use App\Form\CommentFormType;
use App\Form\TicketFormType;
use App\Repository\CommentRepository;
use App\Repository\StatutTicketRepository;
use App\Repository\TicketRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/ticket')]
class TicketController extends AbstractController
{
    #[Route('/', name: 'app_admin_ticket', methods: ['GET'])]
    public function index(TicketRepository $ticketRepository): Response
    {
        $tickets = $ticketRepository->findBy([],['updated_at'=>'DESC']);
        $datas = [];

        for ($i=0;$i<count($tickets);$i++){
            $datas[$i]['id'] = $tickets[$i]->getId();
            $datas[$i]['data'] = [];
            $tags = [];
            foreach ($tickets[$i]->getTags() as $tag){
                array_push($tags, $tag->getName());
            }
            $array = [
                $tickets[$i]->getTitle(),
                $tickets[$i]->getAuthor()->getUsername(),
                $tags,
                $tickets[$i]->getCreatedAt()->format('d/m/Y')
            ];
            array_push($datas[$i]['data'],$array);
        }

        return $this->render('admin/ticket/index.html.twig', [
            'datas' => $datas
        ]);
    }

    #[Route('/new', name: 'app_admin_ticket_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TicketRepository $ticketRepository): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketFormType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setUuid(Uuid::uuid4()->toString());
            $ticket->setCreatedAt(new \DateTimeImmutable());
            $ticket->setUpdatedAt(new \DateTimeImmutable());
            $ticketRepository->add($ticket);
            $this->addFlash('success', 'Ticket ajouté avec succès');
            return $this->redirectToRoute('app_admin_ticket', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/ticket/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_ticket_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, TicketRepository $ticketRepository, Ticket $ticket): Response
    {
        $form = $this->createForm(TicketFormType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticketRepository->add($ticket);
            $ticket->setUpdatedAt(new \DateTimeImmutable());
            $this->addFlash('success', 'Ticket modifié avec succès');
            return $this->redirectToRoute('app_admin_ticket', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/ticket/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/comment', name: 'app_admin_ticket_comment', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function comment(Request $request, Ticket $ticket, CommentRepository $commentRepository): Response
    {
        $form = $this->createForm(TicketFormType::class, $ticket);
        $form->handleRequest($request);

        $comments = $commentRepository->findBy(['ticket'=>$ticket],['updated_at'=>'DESC']);

        return $this->render('admin/ticket/comment/index.html.twig', [
            'ticket' => $ticket,
            'comments' => $comments,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/comment/{idComment}/edit', name: 'app_admin_ticket_comment_edit', requirements: ['id' => '\d+', 'idComment' => '\d+'], methods: ['GET', 'POST'])]
    public function commentEdit(
        #[MapEntity(mapping:['id'=>'id'])] Ticket $ticket,
        #[MapEntity(mapping:['idComment'=>'id'])] Comment $comment,
        Request $request, TicketRepository $ticketRepository, CommentRepository $commentRepository, $id, $idComment): Response
    {
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUpdatedAt(new \DateTime());
            $commentRepository->add($comment);
            $this->addFlash('success', 'Commentaire modifié avec succès');
            return $this->redirectToRoute('app_admin_ticket_comment', ['id'=>$ticket->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/ticket/comment/edit.html.twig', [
            'ticket' => $ticket,
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/comment/{idComment}', name: 'app_admin_ticket_comment_delete', requirements: ['id' => '\d+', 'idComment' => '\d+'], methods: ['POST'])]
    public function commentDelete(
        #[MapEntity(mapping:['id'=>'id'])] Ticket $ticket,
        #[MapEntity(mapping:['idComment'=>'id'])] Comment $comment,
        Request $request, CommentRepository $commentRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $commentRepository->remove($comment);
            $this->addFlash('success', 'Commentaire supprimé avec succès');
        }
        return $this->redirectToRoute('app_admin_ticket_comment', ['id'=>$ticket->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_admin_ticket_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, Ticket $ticket, TicketRepository $ticketRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ticket->getId(), $request->request->get('_token'))) {
            $ticketRepository->remove($ticket);
            $this->addFlash('success', 'Ticket supprimé avec succès');
        }
        return $this->redirectToRoute('app_admin_ticket', [], Response::HTTP_SEE_OTHER);
    }
}
