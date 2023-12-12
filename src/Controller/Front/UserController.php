<?php

namespace App\Controller\Front;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('front/user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/{id}', name: 'app_user_show')]
    public function show($id, UserRepository $userRepository): Response
    {
        $user = $userRepository->findOneBy(['id'=>$id]);
        return $this->render('front/user/show.html.twig', [
            'user' => $user
        ]);
    }
}
