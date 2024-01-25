<?php

namespace App\Controller\Front;

use App\Form\SearchFormType;
use App\Repository\TagRepository;
use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function search(Request $request, TicketRepository $ticketRepository, TagRepository $tagRepository): Response
    {
        $tickets = $ticketRepository->findBy([],['updated_at'=>'DESC']);

        $defaultData = null;
        $nameSearch = "";
        $tagSearch = "";
        if($request->query->get('nameSearch') or $request->query->get('tagSearch')){
            // Récupérer les données du formulaire de la home page
            $nameSearch = $request->query->get('nameSearch');
            $tagSearch = $request->query->get('tagSearch');
            $defaultData = ['name'=>$nameSearch, 'tag'=>$tagRepository->find($tagSearch)];
        }

        $form = $this->createForm(SearchFormType::class, $defaultData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $nameSearch = strtolower($form->getData()['name']);
            $tagSearch = $form->getData()['tag'];
            if(!empty($tagSearch)){
                $tagSearch = $tagSearch->getId();
            }
        }
        $ticketsFilter = array_filter($tickets, function ($ticket) use ($nameSearch, $tagSearch) {
            $titleContains = strpos(strtolower($ticket->getTitle()), $nameSearch) !== false;
            if ($titleContains && !empty($tagSearch)) {
                foreach ($ticket->getTags() as $tag) {
                    if ($tag->getId() == $tagSearch) {
                        return true;
                    }
                }
                return false;
            }
            return $titleContains;
        });
        $tickets = $ticketsFilter;


        return $this->render('front/search/index.html.twig', [
            'form' => $form->createView(),
            'tickets' => $tickets
        ]);
    }


}
