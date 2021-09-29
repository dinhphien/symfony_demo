<?php

namespace App\Controller;

use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

#[Route('/blog')]
class CommentController extends AbstractController
{
    #[Route('/{id}/comment', name: 'comment_list', methods: 'GET', requirements: ['id' => '\d+'])]
    public function index(int $id): Response
    {
        $repository = $this->getDoctrine()->getRepository(Comment::class);
        $comments = $repository->findBy(['blogPost' => $id]);
        /**@var Serializer */
        $serializer = $this->get('serializer');
        $data = $serializer->normalize($comments, 'json', ['groups' => 'get-comment-with-author']);
        return $this->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
