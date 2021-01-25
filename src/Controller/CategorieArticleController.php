<?php

namespace App\Controller;

use App\Entity\CategorieArticle;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Type\CategorieArticleType;

class CategorieArticleController extends AbstractController
{
    /**
     * @Route("/categories", name="categories")
     */
    public function index(): Response
    {
        $categorie = $this->getDoctrine()
            ->getRepository(CategorieArticle::class)
            ->findAll();

        return $this->render('categorie_article/index.html.twig', [
            'controller_name' => 'CategorieArticleController',
            'categories' => $categorie
        ]);
    }

     /**
     * @Route("/categories/add", name="categories_add")
     */
    public function create(Request $request): Response
    {
        $categorie = new CategorieArticle();

        $form = $this->createForm(CategorieArticleType::class, $categorie);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // save the form data in $categorie
            $categorie = $form->getData();
            $categorie->setTimestamps(new \DateTime('now'));
            // save new categorie in db
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($categorie);
            $entityManager->flush();

            return $this->redirectToRoute('categories');
        }

        return $this->render('categorie_article/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/categories/edit/{id}", name="categories_edit")
     */
    public function update(int $id, Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $categorie = $entityManager->getRepository(CategorieArticle::class)->find($id);

        if (!$categorie) {
            throw $this->createNotFoundException(
                'There is no category for this id: '.$id
            );
        }

        $form = $this->createForm(CategorieArticleType::class, $categorie);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // save the form data in $categorie
            $categorie = $form->getData();
            $categorie->setTimestamps(new \DateTime('now'));
            // save new categorie in db
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($categorie);
            $entityManager->flush();

            return $this->redirectToRoute('categories');
        }

        return $this->render('categorie_article/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/categories/delete/{id}", name="categories_delete")
     */
    public function delete(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $categorie = $entityManager->getRepository(CategorieArticle::class)->find($id);

        if (!$categorie) {
            throw $this->createNotFoundException(
                'There is no category for this id: '.$id
            );
        }

        $entityManager->remove($categorie);
        $entityManager->flush();
        $this->addFlash('success', 'Your category has been successfully deleted!');

        return $this->redirectToRoute('categories');
    }
}
