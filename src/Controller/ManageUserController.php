<?php

namespace App\Controller;

use App\Entity\User;

use App\Utils\ModifyObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class ManageUserController extends AbstractController
{
    /**
     * @Route("/users", name="add_user", methods={"POST"})
     */
    public function addUser(Request $request, SerializerInterface $serializer,
        ValidatorInterface $validator, UserInterface $client
    ): Response {

        $json = $request->getContent();

        //Verify $json is not blank
        if ($json == null) {
            return $this->json([
                'status' => 400 . ': Bad Request',
                'message' => "Les données json sont inexistantes."
            ], 400);
        }

        //Try to deserialize
        try {
            $user = $serializer->deserialize($json, User::class, 'json');

        } catch (NotEncodableValueException $e) {
            return $this->json([
                'status' => 400 . ': Bad Request',
                'message' => $e->getMessage()
            ], 400);
        }
        $user->setClient($client)
             ->setDateAtCreated(new \Datetime() )
        ;
        
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
     * @Route("/user/{id}", name="update_user", methods={"PUT", "PATCH"})
     */
    public function modifyUser(Request $request, User $user,
        ValidatorInterface $validator, ModifyObject $modifyObject
    ): Response {

        $json = $request->getContent();
        $verb = $request->getMethod();

        if ($json == null) {
            return $this->json([
                'status' => 400 . ': Bad Request',
                'message' => "Les données json sont inexistantes."
            ], 400);
        }

        try {
            $userModified = $modifyObject->update($user, $json, $verb);

        } catch (NotEncodableValueException $e) {
            return $this->json([
                'status' => 400 . ': Bad Request',
                'message' => $e->getMessage()
            ], 400);
        }

        //We use the validator component for check all entries in user entity
        $errors = $validator->validate($user);
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
}
