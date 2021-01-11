<?php

namespace App\Controller;

use App\Entity\User;

use App\Utils\DataControl;
use App\Utils\ModifyObject;
use OpenApi\Annotations as OA;
use App\Exception\NoJsonBodyException;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

/**
 * @OA\Response(
 *     response=400,
 *     description="When the request is not valid",
 *     @OA\JsonContent(
 *        type="object",
 *        example={"status": "400: Bad Request", "message": "{ message for invalid request }"})
 *     )
 * )
 */
class ManageUserController extends AbstractController
{
    /**
     * @Route("/users", name="add_users", methods={"POST"})
     * 
     * @OA\Response(
     *     response=201,
     *     description="For create a new user linked to a client. Returns Success Message with the just created element",
     *     @OA\JsonContent(
     *        type="object",
     *        example={"status": "201: Created", "message": "Un utilisateur a été ajouté."})
     *     )
     * )
     * @OA\Parameter(
     *     name="in_request_body",
     *     in="header",
     *     description="Add a new user in the database - Json format",
     *     required=true,
     *     @OA\Schema(
     *              type="object",
     *              @OA\Property(property="username", type="string", example="johndoe654"),
     *              @OA\Property(property="email", type="string", example="johndoe654@orange.fr"),
     *              @OA\Property(property="tel", type="string", example="0654586987"),
     *              @OA\Property(property="profilPicture", type="string", example="my_picture.jpg"),
     *              @OA\Property(property="adress", type="string", example="154, rue des bouleaux 44450 Paris")
     *          )
     *     ) 
     * )
     * @OA\Tag(name="users")
     * @Security(name="Bearer")
     */
    public function addUser(Request $request, SerializerInterface $serializer,
        ValidatorInterface $validator, DataControl $dataControl
    ): Response {

        $json = $request->getContent();

        //Verify $json is not blank
        if ($json == null) {
            throw new NoJsonBodyException;
        }

        //We check if all data are in json
        $dataControl->userControl(json_decode($json, true));

        $user = $serializer->deserialize($json, User::class, 'json');

        $user->setClient($this->getUser())
             ->setDateAtCreated(new \DateTime());
        
        //We use the validator component for check all entries in user entity
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($user);
        $manager->flush();

        return $this->json([
            'status' => 201 . ': Created',
            'message' => 'Un utilisateur a été ajouté.'
        ], 200);
    }

    /**
     * @Route("/users/{id}", name="update_users", methods={"PUT", "PATCH"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="For update a user linked to a client. Returns Success Message with the just updated element",
     *     @OA\JsonContent(
     *        type="object",
     *        example={"status": "200: Success", "message": "L'utilisateur {user_id} a été modifié."})
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
     *     name="in_request_body",
     *     in="header",
     *     description="Add properties for replace or update a user - Json format - 
                        Warning : For PUT method, the body require all the properties of the User. 
                        For PATCH method, it's not important. 
                        In other words, for replace a user, use PUTCH method and for update a user, use PATCH method.",
     *     required=true,
     *     @OA\Schema(
     *              type="object",
     *              @OA\Property(property="username", type="string", example="johndoe654"),
     *              @OA\Property(property="email", type="string", example="johndoe654@orange.fr"),
     *              @OA\Property(property="tel", type="string", example="0654586987"),
     *              @OA\Property(property="profilPicture", type="string", example="my_picture.jpg"),
     *              @OA\Property(property="adress", type="string", example="154, rue des bouleaux 44450 Paris")
     *          )
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
    public function modifyUser(Request $request,ValidatorInterface $validator, 
        ModifyObject $modifyObject, User $user
    ): Response {

        if ($user->getClient() != $this->getUser()) {
            throw new NotFoundHttpException;
        }

        $json = $request->getContent();

        //Verify $json is not blank
        if ($json == null) {
            throw new NoJsonBodyException;
        }

        $userModified = $modifyObject->update($user, $json);
    
        //We use the validator component for check all entries in user entity
        $errors = $validator->validate($userModified);
        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }
        
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($userModified);
        $manager->flush();
        
        return $this->json([
            'status' => 200 . ': Success',
            'message' => "L'utilisateur " .  $userModified->getId() . " a été modifié."
        ], 200);
    }

    /**
     * @Route("/users/{id}", name="delete_users", methods={"DELETE"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="For remove a user linked to a client. Returns Success Message.",
     *     @OA\JsonContent(
     *        type="object",
     *        example={"status": "200: Success", "message": "L'utilisateur {user_id} a été supprimé."})
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
    public function deleteUser(User $user)
    {
        if ($user->getClient() != $this->getUser()) {
            throw new NotFoundHttpException;
        }
        $id = $user->getId();

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($user);
        $manager->flush();

        return $this->json([
            'status' => 200 . ': Success',
            'message' => "L'utilisateur $id a été supprimé."
        ], 200);
    }
}
