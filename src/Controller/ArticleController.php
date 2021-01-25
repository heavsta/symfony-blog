<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Type\ArticleType;

class ArticleController extends AbstractController
{
    /**
     * @Route("/articles", name="articles")
     */
    public function index(): Response
    {
        $article = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAll();

        return $this->render('articles/index.html.twig', [
            'controller_name' => 'ArticleController',
            'articles' => $article
        ],);
    }

    /**
     * @Route("/articles/add", name="articles_add")
     */
    public function create(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // save the form data in $article
            $article = $form->getData();
            $article->setTimestamps(new \DateTime('now'));
            // save new article in db
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Your changes were saved!'
            );

            return $this->redirectToRoute('articles');
        }

        return $this->render('articles/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/articles/edit/{id}", name="articles_edit")
     */
    public function update(int $id, Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $article = $entityManager->getRepository(Article::class)->find($id);

        if (!$article) {
            throw $this->createNotFoundException(
                'There is no article for this id: '.$id
            );
        }

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // save the form data in $article
            $article = $form->getData();
            $article->setTimestamps(new \DateTime('now'));
            // save new article in db
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Your changes were saved!'
            );

            return $this->redirectToRoute('articles');
        }

        return $this->render('articles/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/articles/show/{id}", name="articles_show")
     */
    public function show(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $article = $entityManager->getRepository(Article::class)->find($id);

        return $this->render('articles/show.html.twig', [
            'article' => $article
        ]);
    }

    /**
     * @Route("/articles/delete/{id}", name="articles_delete")
     */
    public function delete(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $article = $entityManager->getRepository(Article::class)->find($id);

        if (!$article) {
            throw $this->createNotFoundException(
                'There is no article for this id: '.$id
            );
        }

        $entityManager->remove($article);
        $entityManager->flush();
        $this->addFlash('success', 'Your article has been successfully deleted!');

        return $this->redirectToRoute('articles');
    }
}
