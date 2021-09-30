<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthenticationController extends AbstractController
{
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/AuthenticationController.php',
        ]);
    }

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(): Response
    {
        return $this->json([]);
    }

    #[Route('/me', name: 'me', methods: ['GET'])]
    public function me(): Response
    {
        $user = $this->getUser();
        $data = $this->get('serializer')->normalize($user, 'json', ['groups' => 'get']);
        return $this->json([
            'success' => true,
            'data' => $data
        ]);
    }

    #[Route('/logout', name: 'logout', methods: ['GET'])]
    public function logout(): Response
    {
        return $this->json([]);
    }
}
