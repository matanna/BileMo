<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Client;
use App\Response\FormatResponse;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class GetUsersController extends AbstractController
{
    /**
     * @Route("/users", name="list_users", methods={"GET"})
     */
    public function getUsers(
        UserRepository $userRepository, Request $request, UserInterface $client
    ): Response {

        try {
            $users = $userRepository->findUsersByClient($client, $request);
            
        } catch (\Exception $e) {
            return $this->json([
                'status' => 400 . ": Bad Request",
                'message' => $e->getMessage()
            ], 
            400);
        }

        if ($users == []) {
            return $this->json([
                'status' => 200 . ": Success",
                'message' => "Aucun résultat pour cette requête."
            ],
            200);
        }
        
        return $this->json($users, 200, [], [
            'groups' => 'list_users'
        ]);
    }

    /**
     * @Route("/user/{id}", name="show_user", methods={"GET"})
     */
    public function showUser(FormatResponse $formatResponse, NormalizerInterface $normalizer, User $user = null): Response 
    {
        if ($user == null) {
            return $this->json([
                'status' => 404 . ": Page not Found",
                'message' => "Cette ressource n'existe pas."
            ], 404);
        }

        $userNormalize = $normalizer->normalize($user, null, ['groups' => 'show_user']);
        
        $userFormated = $formatResponse->format($userNormalize);  

        return $this->json($userFormated, 200);
    }
}