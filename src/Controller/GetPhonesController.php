<?php

namespace App\Controller;

use App\Repository\PhoneRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GetPhonesController extends AbstractController
{
    /**
     * @Route("/phones", name="get_phones", methods={"GET"})
     */
    public function getPhones(PhoneRepository $phoneRepository): Response
    {
        $phones = $phoneRepository->findAll();

        return $this->json($phones, 200, [],[
                'groups' => 'get:phones'
            ]
        );
    }
}
