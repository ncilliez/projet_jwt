<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{

    #[Route('/api/user', name: 'get_user')]
    public function getUserInfo(UserRepository $userRepository, SerializerInterface $serializer, Security $security): JsonResponse
    {
        
        $currentUser = $security->getUser();

        // Vérifier si l'utilisateur est connecté
        if (!$currentUser instanceof User) {
            return new JsonResponse(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }
        
        $userList = null;
        // Vérifier le rôle de l'utilisateur
        if (in_array('ROLE_ADMIN', $currentUser->getRoles(), true)) {
            // Si l'utilisateur est administrateur, récupérer la liste complète des utilisateurs
            $userList = $userRepository->findAll();                    
        } else {
            // Si l'utilisateur n'est pas administrateur, renvoyer uniquement ses informations
            $userList = $userRepository->find($currentUser->getId());
        }

        $jsonUserDetails = $serializer->serialize($userList, 'json');

        return new JsonResponse($jsonUserDetails, Response::HTTP_OK, [], true);
    }
}


