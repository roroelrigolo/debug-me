<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/account')]
class AccountController extends AbstractController
{
    #[Route('/{username}', name: 'app_account_show')]
    public function show(
        User $user, UserRepository $userRepository): Response
    {
        return $this->render('front/account/show.html.twig', [
            'user' => $user
        ]);
    }
}
