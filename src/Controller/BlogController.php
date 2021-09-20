<?php

namespace App\Controller;

use App\Entity\BlogPost;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

#[Route('/blog')]
class BlogController extends AbstractController
{
    /**@var LoggerInterface */
    private $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/', name:'blog_list', methods:['GET'])]
    public function list(): Response
    {
        $this->logger->debug("Fetching all blogs!");
        $repository = $this->getDoctrine()->getRepository(BlogPost::class);
        $items = $repository->findAll();
        return $this->json([
            "data" => $items
        ]);
    }

    #[Route('/post/{id}', name:'blog_by_id', methods:['GET'], requirements:['id' => '\d+'])]
    public function post(BlogPost $post): Response
    {
        return $this->json($post);
    }

    #[Route('/create', name:'blog_create', methods:['POST'])]
    public function create(Request $request): Response
    {
        /**@var Serializer $serializer*/
        $serializer = $this->get('serializer');
        $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($blogPost);
        $entityManager->flush();
        return $this->json($blogPost);
    }

    #[Route('/delete/{id}', name:'blog_delete', methods:['DELETE'], requirements:['id' => '\d+'])]
    public function delete(BlogPost $blogPost): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($blogPost);
        $entityManager->flush();
        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
