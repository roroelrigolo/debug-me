<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RankingController extends AbstractController
{
    #[Route('/', name: 'app_ranking')]
    public function index(): Response
    {
        return $this->render('front/ranking/index.html.twig', [

        ]);
    }
}
