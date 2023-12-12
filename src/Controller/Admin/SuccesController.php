<?php

namespace App\Controller\Admin;

use App\Entity\Succes;
use App\Form\SuccesFormType;
use App\Repository\SuccesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/succes')]
class SuccesController extends AbstractController
{
    #[Route('/', name: 'app_admin_succes')]
    public function index(SuccesRepository $succesRepository): Response
    {
        $succes = $succesRepository->findBy([],['name'=>'ASC']);
        $datas = [];

        for ($i=0;$i<count($succes);$i++){
            $datas[$i]['id'] = $succes[$i]->getId();
            $datas[$i]['data'] = [];
            $array = [
                $succes[$i]->getName(),
                $succes[$i]->getGoal()
            ];
            array_push($datas[$i]['data'],$array);
        }

        return $this->render('admin/succes/index.html.twig', [
            'datas' => $datas
        ]);
    }

    #[Route('/new', name: 'app_admin_succes_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SuccesRepository $succesRepository): Response
    {
        $succes = new Succes();
        $form = $this->createForm(SuccesFormType::class, $succes);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $succesRepository->add($succes);
            $this->addFlash('success', 'Succès ajouté avec succès');

            return $this->redirectToRoute('app_admin_succes', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/succes/new.html.twig', [
            'succes' => $succes,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_succes_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SuccesRepository $succesRepository, Succes $succes): Response
    {
        $form = $this->createForm(SuccesFormType::class, $succes);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $succesRepository->add($succes);
            $this->addFlash('success', 'Succès modifié avec succès');

            return $this->redirectToRoute('app_admin_succes', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/succes/edit.html.twig', [
            'succes' => $succes,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_succes_delete', methods: ['POST'])]
    public function delete(Request $request, Succes $succes, SuccesRepository $succesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$succes->getId(), $request->request->get('_token'))) {
            $succesRepository->remove($succes);
            $this->addFlash('success', 'Succès supprimé avec succès');
        }
        return $this->redirectToRoute('app_admin_succes', [], Response::HTTP_SEE_OTHER);
    }
}
