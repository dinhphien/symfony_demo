<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Helper\ErrorParser;
use App\Requests\ListBlogPostRequest;
use App\Services\RequestValidatorService;
use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/blog')]
class BlogController extends AbstractController
{
    /**@var LoggerInterface */
    private $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('', name: 'blog_list', methods: ['GET'])]
    public function index(ListBlogPostRequest $request): Response
    {
        $this->logger->debug("Fetching all blogs!");

        $page = $request->getPage();
        $limit = $request->getLimit();

        $repository = $this->getDoctrine()->getRepository(BlogPost::class);
        $items = $repository->getBlogPost($page, $limit);
        /**@var Serializer $serializer*/
        $serializer = $this->get('serializer');
        $data = $serializer->normalize($items, 'json', ['groups' => 'get-blog-post-with-author']);
        return $this->json([
            'success' => true,
            'data' => $data
        ]);
    }

    #[Route('/{id}', name: 'blog_by_id', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function post(BlogPost $post): Response
    {
        $data = $this->get('serializer')->normalize($post, 'json', ['groups' => 'get-detail-blog-post']);
        return $this->json([
            'success' => true,
            'data' => $data
        ]);
    }

    #[Route('/create', name: 'blog_create', methods: ['POST'])]
    public function create(Request $request, ValidatorInterface $validator): Response
    {
        /**@var Serializer $serializer*/
        $serializer = $this->get('serializer');
        $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');
        $blogPost->setPublished(new DateTime());
        $blogPost->setAuthor($this->getUser());

        $violations = $validator->validate($blogPost);
        if ($violations->count() > 0) {
            return $this->json([
                'success' => false,
                'message' => ErrorParser::parseConstraintViolations($violations)
            ], 400);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($blogPost);
        $entityManager->flush();

        $data = $this->get('serializer')->normalize($blogPost, 'json', ['groups' => 'get-detail-blog-post']);
        return $this->json([
            'success' => true,
            'data' => $data
        ]);
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
