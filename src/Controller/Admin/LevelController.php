<?php

namespace App\Controller\Admin;


use App\Entity\Level;
use App\Form\LevelFormType;
use App\Repository\LevelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/level')]
class LevelController extends AbstractController
{
    #[Route('/', name: 'app_admin_level')]
    public function index(LevelRepository $levelRepository): Response
    {
        $levels = $levelRepository->findBy([],['step'=>'ASC']);
        $datas = [];

        for ($i=0;$i<count($levels);$i++){
            $datas[$i]['id'] = $levels[$i]->getId();
            $datas[$i]['data'] = [];
            $array = [
                $levels[$i]->getName(),
                $levels[$i]->getStep(),
                $levels[$i]->getStage()
            ];
            array_push($datas[$i]['data'],$array);
        }

        return $this->render('admin/level/index.html.twig', [
            'datas' => $datas
        ]);
    }

    #[Route('/new', name: 'app_admin_level_new', methods: ['GET', 'POST'])]
    public function new(Request $request, LevelRepository $levelRepository): Response
    {
        $level = new Level();
        $form = $this->createForm(LevelFormType::class, $level);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $levelRepository->add($level);
            $this->addFlash('success', 'Niveau ajouté avec succès');
            return $this->redirectToRoute('app_admin_level', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/level/new.html.twig', [
            'level' => $level,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_level_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, LevelRepository $levelRepository, Level $level): Response
    {
        $form = $this->createForm(LevelFormType::class, $level);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $levelRepository->add($level);
            $this->addFlash('success', 'Niveau modifié avec succès');
            return $this->redirectToRoute('app_admin_level', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/level/edit.html.twig', [
            'level' => $level,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_level_delete', methods: ['POST'])]
    public function delete(Request $request, Level $level, LevelRepository $levelRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$level->getId(), $request->request->get('_token'))) {
            $levelRepository->remove($level);
            $this->addFlash('success', 'Niveau supprimé avec succès');
        }
        return $this->redirectToRoute('app_admin_level', [], Response::HTTP_SEE_OTHER);
    }
}
