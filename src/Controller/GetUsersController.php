<?php

namespace App\Controller;

use App\Entity\User;
use OpenApi\Annotations as OA;
use App\Response\FormatResponse;
use App\Repository\UserRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Response(
 *     response=400,
 *     description="When existing a problem in a request",
 *     @OA\JsonContent(
 *        type="object",
 *        example={"status": "400: Bad Request", "message": "{ message for invalid request }"})
 *     )
 * )
 */
class GetUsersController extends AbstractController
{
    /**
     * @Route("/users", name="list_users", methods={"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns the list of users linked to a client with pagination system",
     *     @OA\JsonContent(
     *        type="object",
     *        example={
     *              "id": 853,
     *              "username": "johndoe654",
     *              "email": "johndoe654@orange.fr"
     *          }
     *     )
     * )
     * @OA\Response(
     *     response=204,
     *     description="When returns an empty result",
     *     @OA\JsonContent(
     *        type="object",
     *        example={"status": "204: No Content", "message": "Aucun résultat pour cette requête."})
     *     )
     * )
     * @OA\Parameter(
     *     name="byusername",
     *     in="query",
     *     description="Try results by username of users in alphabetical order",
     *     @OA\Schema(type="{ASC, DESC}", example="#/users?byusername=DESC")
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="query",
     *     description="Try results by the id of users",
     *     @OA\Schema(type="{ASC, DESC}", example="#/users?byid=DESC")
     * )
     * @OA\Parameter(
     *     name="search",
     *     in="query",
     *     description="Search a user by username with a string of characters",
     *     @OA\Schema(type="string", example="#/users?search=foo")
     * )
     * @OA\Parameter(
     *     name="perpage",
     *     in="query",
     *     description="Define the number of element per page - default = 10",
     *     @OA\Schema(type="integer", example="#/users?perpage=8")
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="Define the page - default = 1",
     *     @OA\Schema(type="integer", example="#/users?page=2")
     * )
     * @OA\Tag(name="users")
     * @Security(name="Bearer")
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
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns details of one user linked to a client",
     *     @OA\JsonContent(
     *        type="object",
     *        example={
     *               "id": 853,
     *              "username": "johndoe654",
     *              "email": "johndoe654@orange.fr",
     *               "roles": {
     *                  "ROLE_USER"
     *               },
     *               "dateAtCreated": "2021-01-14T10:27:38.251Z",
     *               "tel": "0645785859",
     *               "profilPicture": "my_picture.jpg",
     *               "adress": "154, rue des bouleaux 44450 Paris"
     *         }
     *     )
     * )
     * @OA\Response(
     *     response=404,
     *     description="When {id} dosen't exist",
     *     @OA\JsonContent(
     *        type="object",
     *        example={"status": "404: Not Found", "message": "Cette ressource n'existe pas"})
     *     )
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="The id of one user",
     *     @OA\Schema(type="integer", example="#/users/695")
     * )
     * @OA\Tag(name="users")
     * @Security(name="Bearer")
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
