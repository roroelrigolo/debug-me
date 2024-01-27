<?php

namespace App\Controller\Front;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RankingController extends AbstractController
{
    #[Route('/ranking', name: 'app_ranking', methods: ['GET'])]
    public function rankingPoints(UserRepository $userRepository): Response
    {
        $users = $userRepository->findBy([],['points'=>'DESC']);
        return $this->render('front/ranking/index.html.twig', [
            'users' => $users
        ]);
    }
}


