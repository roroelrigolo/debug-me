<?php

namespace App\Controller\Front;

use App\Repository\UserRepository;
use App\Repository\TicketRepository;
use App\Form\SearchFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, UserRepository $userRepository, TicketRepository $ticketRepository): Response
    {

        $users = $userRepository->findBy([],['points'=>'DESC'], 3);
        $tickets = $ticketRepository->findBy([], [], 4);

        $form = $this->createForm(SearchFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nameSearch = strtolower($form->getData()['name']);
            $tagSearch = $form->getData()['tag'];
            if(!empty($tagSearch)){
                $tagSearch = $tagSearch->getId();
            }
            return $this->redirectToRoute('app_search', [
                    'nameSearch' => $nameSearch,
                    'tagSearch' => $tagSearch
            ]);
        }

        return $this->render('front/home/index.html.twig', [
            'users' => $users,
            'tickets' => $tickets,
            'form' => $form->createView(),
        ]);
    }
}
