<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Type\ArticleType;
use App\Form\Type\CommentFormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

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
        ]);
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

        $comment = new Comment;
        $form = $this->createForm(CommentFormType::class, $comment);

        return $this->render('articles/show.html.twig', [
            'article' => $article,
            'comment_form' => $form->createView()
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

    /**
     * @Route("/articles/json", name="articles_json")
     */
    public function getJson(): JsonResponse
    {
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAll();

        $encoder = new JsonEncoder();
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getTitre();
            },
        ];
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);
        $serializer = new Serializer([$normalizer], [$encoder]);
        $jsonArticle = $serializer->serialize($articles, 'json');

        $response = JsonResponse::fromJsonString($jsonArticle);
        // Allow all websites
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }

    /**
     * @Route("/articles/json/{id}", name="articles_json_id")
     */
    public function getJsonById(int $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $article = $entityManager->getRepository(Article::class)->find($id);

        $encoder = new JsonEncoder();
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getTitre();
            },
        ];
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);
        $serializer = new Serializer([$normalizer], [$encoder]);
        $jsonArticle = $serializer->serialize($article, 'json');

        $response = JsonResponse::fromJsonString($jsonArticle);
        // Allow all websites
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }

    /**
     * @Route("/articles/json-add", name="articles_json_add", methods={"POST"})
     */
    public function addToJson(Request $request, SerializerInterface $serializer)
    {

        $data = $request->toArray();

        // serialize $data
        $data = $serializer->serialize($data, 'json');
        // deserialize $data
        $data = $serializer->deserialize($data, Article::class, 'json');

        $data->setTimestamps(new \DateTime());
        // save new article in db
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($data);
        $entityManager->flush();
        return new Response();
    }

    /**
     * @Route("/articles/json-edit/{id}", name="articles_json_edit", methods={"PUT"})
     */
    public function editJson(int $id, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $article = $entityManager->getRepository(Article::class)->find($id);

        if (!$article) {
            throw $this->createNotFoundException(
                'There is no article for this id: '.$id
            );
        }

        $data = $request->toArray();

        $article->setTitre($data["titre"]);
        $article->setContenu($data["contenu"]);
        $article->setPicture($data["picture"]);
        $article->setTimestamps(new \DateTime());
        $article->setVisible(true);
        //save edit in db
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($article);
        $entityManager->flush();

        return new Response();
    }
}
