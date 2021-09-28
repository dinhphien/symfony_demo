<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Requests\BaseValidatingRequest;
use App\Requests\ListBlogPostRequest;
use App\Services\RequestValidatorService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

#[Route('/blog')]
class BlogController extends AbstractController
{
    /**@var LoggerInterface */
    private $logger;
    private $validator;
    public function __construct(LoggerInterface $logger, RequestValidatorService $validator)
    {
        $this->logger = $logger;
        $this->validator = $validator;
    }

    #[Route('/', name: 'blog_list', methods: ['GET'])]
    public function list(Request $request): Response
    {
        $this->logger->debug("Fetching all blogs!");
        $listBlogRequest = new ListBlogPostRequest($request);
        $this->validator->validate($listBlogRequest);

        $page = $listBlogRequest->getPage();
        $limit = $listBlogRequest->getLimit();

        $repository = $this->getDoctrine()->getRepository(BlogPost::class);


        $items = $repository->getBlogPost($page, $limit);
        /**@var Serializer $serializer*/
        $serializer = $this->get('serializer');
        $result = [
            'success' => true,
            'data' => $items
        ];
        $data = $serializer->serialize($result, 'json', ['groups' => 'get-blog-post-with-author']);
        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/post/{id}', name: 'blog_by_id', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function post(BlogPost $post): Response
    {
        return $this->json([
            'success' => true,
            'data' => $post
        ]);
    }

    #[Route('/create', name: 'blog_create', methods: ['POST'])]
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

    #[Route('/delete/{id}', name: 'blog_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(BlogPost $blogPost): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($blogPost);
        $entityManager->flush();
        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
