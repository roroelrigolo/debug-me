<?php

namespace App\Controller\Front;

use App\Repository\TagRepository;
use App\Repository\UserRepository;
use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(TagRepository $tagRepository, UserRepository $userRepository, TicketRepository $ticketRepository): Response
    {
        $tags = $tagRepository->findAll();
        $users = $userRepository->findBy([],['points'=>'DESC'], 3);
        $tickets = $ticketRepository->findBy([], [], 4);

        return $this->render('front/home/index.html.twig', [
            'tags' => $tags,
            'users' => $users,
            'tickets' => $tickets,
        ]);
    }
}
