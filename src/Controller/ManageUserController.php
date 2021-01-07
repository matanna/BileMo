<?php

namespace App\Controller;

use App\Entity\User;

use App\Utils\DataControl;
use App\Utils\ModifyObject;
use App\Exception\NoJsonBodyException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class ManageUserController extends AbstractController
{
    /**
     * @Route("/users", name="add_users", methods={"POST"})
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
            'status' => 201 . ': Created',
            'message' => "L'utilisateur " .  $userModified->getId() . " a été modifié."
        ], 201);
    }

    /**
     * @Route("/users/{id}", name="delete_users", methods={"DELETE"})
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
            'status' => 201 . ': Created',
            'message' => "L'utilisateur $id a été supprimé."
        ], 200);
    }
}
