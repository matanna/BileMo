<?php

namespace App\Controller;

use App\Entity\User;
use App\Response\FormatResponse;
use App\Repository\UserRepository;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetUsersController extends AbstractController
{
    /**
     * @Route("/users", name="list_users", methods={"GET"})
     */
    public function getUsers(
        UserRepository $userRepository, UserInterface $client, Request $request, CacheInterface $cache
    ): Response {

        try {
            
            //The cache feature is disabled because we work with a pgination feature

            //$users = $cache->get('item.users',function(ItemInterface $item) use ($userRepository, $request, $client){

                //the cache is clear at the end of 60s
                //$item->expiresAfter(900);

                $users = $userRepository->findUsersByClient($client, $request);

                //return $users;
            //});
            
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
     * @Route("/users/{id}", name="show_users", methods={"GET"})
     */
    public function showUser(FormatResponse $formatResponse, NormalizerInterface $normalizer, User $user): Response 
    {
        if ($user->getClient() != $this->getUser()) {
            throw new NotFoundHttpException;
        }

        $userNormalize = $normalizer->normalize($user, null, ['groups' => 'show_user']);
        
        $userFormated = $formatResponse->format($userNormalize);  

        return $this->json($userFormated, 200);
    }
}
