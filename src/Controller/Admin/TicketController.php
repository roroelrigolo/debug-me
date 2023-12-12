<?php

namespace App\Controller\Admin;

use App\Entity\Ticket;
use App\Form\TicketFormType;
use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/ticket')]
class TicketController extends AbstractController
{
    #[Route('/', name: 'app_admin_ticket')]
    public function index(TicketRepository $ticketRepository): Response
    {
        $tickets = $ticketRepository->findBy([],['id'=>'DESC']);
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
                $tickets[$i]->getCreatedAt()->format('d/m/Y'),
                'Voir les commentaires'
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
            $ticketRepository->add($ticket);
            $this->addFlash('success', 'Ticket ajouté avec succès');
            return $this->redirectToRoute('app_admin_ticket', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/ticket/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_ticket_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TicketRepository $ticketRepository, Ticket $ticket): Response
    {
        $form = $this->createForm(TicketFormType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticketRepository->add($ticket);
            $this->addFlash('success', 'Ticket modifié avec succès');
            return $this->redirectToRoute('app_admin_ticket', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/ticket/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_ticket_delete', methods: ['POST'])]
    public function delete(Request $request, Ticket $ticket, TicketRepository $ticketRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ticket->getId(), $request->request->get('_token'))) {
            $ticketRepository->remove($ticket);
            $this->addFlash('success', 'Ticket supprimé avec succès');
        }
        return $this->redirectToRoute('app_admin_ticket', [], Response::HTTP_SEE_OTHER);
    }
}
