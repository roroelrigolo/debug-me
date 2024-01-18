<?php

namespace App\Controller\Front;

use App\Form\SearchFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request): Response
    {

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
            'form' => $form->createView(),
        ]);
    }
}
