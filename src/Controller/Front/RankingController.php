<?php

namespace App\Controller\Front;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RankingController extends AbstractController
{
    #[Route('/ranking/points', name: 'app_ranking_points')]
    public function rankingPoints(UserRepository $userRepository): Response
    {
        $users = $userRepository->findBy([],['points'=>'DESC']);
        return $this->render('front/ranking/index.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/ranking/success', name: 'app_ranking_success')]
    public function rankingSuccess(): Response
    {
        return $this->render('front/ranking/index.html.twig', [

        ]);
    }
}


