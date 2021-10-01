<?php

namespace App\Controller;

use App\Entity\User;
use App\Helper\ErrorParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    public function register(Request $request, ValidatorInterface $validator, UserPasswordHasherInterface $passwordEncoder): Response
    {
        $serializer = $this->get('serializer');
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        $violations  = $validator->validate($user);
        if ($violations->count() > 0) {
            return $this->json([
                'success' => false,
                'message' => ErrorParser::parseConstraintViolations($violations)
            ], 400);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $user->setActive(true);
        $user->setPassword(
            $passwordEncoder->hashPassword($user, $user->getPassword())
        );
        $entityManager->persist($user);
        $entityManager->flush();
        $data = $serializer->normalize($user, 'json', ['groups' => 'get']);
        return $this->json([
            'success' => true,
            'data' => $data
        ]);
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
