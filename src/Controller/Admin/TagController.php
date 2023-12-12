<?php

namespace App\Controller\Admin;


use App\Entity\Tag;
use App\Form\TagFormType;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/tag')]
class TagController extends AbstractController
{
    #[Route('/', name: 'app_admin_tag')]
    public function index(TagRepository $tagRepository): Response
    {
        $tags = $tagRepository->findBy([],['name'=>'ASC']);
        $datas = [];

        for ($i=0;$i<count($tags);$i++){
            $datas[$i]['id'] = $tags[$i]->getId();
            $datas[$i]['data'] = [];
            $array = [
                $tags[$i]->getName()
            ];
            array_push($datas[$i]['data'],$array);
        }

        return $this->render('admin/tag/index.html.twig', [
            'datas' => $datas
        ]);
    }

    #[Route('/new', name: 'app_admin_tag_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TagRepository $tagRepository): Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagFormType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tagRepository->add($tag);
            $this->addFlash('success', 'Tag ajouté avec succès');
            return $this->redirectToRoute('app_admin_tag', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/tag/new.html.twig', [
            'tag' => $tag,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_tag_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TagRepository $tagRepository, Tag $tag): Response
    {
        $form = $this->createForm(TagFormType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tagRepository->add($tag);
            $this->addFlash('success', 'Tag modifié avec succès');
            return $this->redirectToRoute('app_admin_tag', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/tag/edit.html.twig', [
            'tag' => $tag,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_tag_delete', methods: ['POST'])]
    public function delete(Request $request, Tag $tag, TagRepository $tagRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tag->getId(), $request->request->get('_token'))) {
            $tagRepository->remove($tag);
            $this->addFlash('success', 'Tag supprimé avec succès');
        }
        return $this->redirectToRoute('app_admin_tag', [], Response::HTTP_SEE_OTHER);
    }
}
