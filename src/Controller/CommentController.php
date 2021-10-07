<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Helper\ErrorParser;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CommentController extends AbstractController
{
    /**@var ValidatorInterface */
    private $validator;
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    #[Route('/blog/{id}/comment', name: 'comment_list', methods: 'GET', requirements: ['id' => '\d+'])]
    public function index(int $id): Response
    {
        $repository = $this->getDoctrine()->getRepository(Comment::class);
        $comments = $repository->findBy(['blogPost' => $id], ['published' => 'DESC']);
        /**@var Serializer */
        $serializer = $this->get('serializer');
        $data = $serializer->normalize($comments, 'json', ['groups' => 'get-comment-with-author']);
        return $this->json([
            'success' => true,
            'data' => $data
        ]);
    }

    #[Route('/blog/{id}/comment', name: 'add_comment', methods: 'POST', requirements: ['id' => '\d+'])]
    public function add(BlogPost $post, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $content = $data['content'] ?? '';
        $comment = new Comment();
        $comment->setContent($content);
        $violations = $this->validator->validate($comment);
        if ($violations->count() > 0) {
            return $this->json([
                'success' => false,
                'message' => ErrorParser::parseConstraintViolations($violations)
            ], 400);
        }
        $comment->setPublished(new DateTime());
        $comment->setAuthor($this->getUser());
        $comment->setBlogPost($post);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($comment);
        $entityManager->flush();
        $result = $this->get('serializer')->normalize($comment, 'json', ['groups' => 'get-comment-with-author']);
        return $this->json([
            'success' => true,
            'data' => $result
        ]);
    }
}
