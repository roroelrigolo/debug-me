<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Form\AccountFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/account')]
class AccountController extends AbstractController
{
    #[Route('/{username}', name: 'app_account_show', requirements: ['username' => '\w+'], methods: ['GET'])]
    public function show(
        User $user, UserRepository $userRepository): Response
    {
        return $this->render('front/account/show.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/{username}/edit', name: 'app_account_edit', requirements: ['username' => '\w+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, UserRepository $userRepository, User $user, SluggerInterface $slugger, Security $security): Response
    {
        if ($user->getUsername() !== $security->getUser()->getUsername()) {
            throw new AccessDeniedException('Access Denied: You are not allowed to access this resource.');
        }

        $form = $this->createForm(AccountFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $picture = $form->get('picture')->getData();
            if ($picture) {
                $originalFilename = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $picture->guessExtension();
                try {
                    $picture->move(
                        $this->getParameter('profile_pictures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {}
                $user->setPicture($newFilename);
            }
            $userRepository->add($user);
            $this->addFlash('notification', ['Votre compte a été modifié avec succès']);
            return $this->redirectToRoute('app_account_show', ['username' => $user->getUsername()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('front/account/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
