<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Response\FormatResponse;
use App\Repository\PhoneRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GetPhonesController extends AbstractController
{
    /**
     * @Route("/phones", name="list_phones", methods={"GET"})
     */
    public function getPhones(PhoneRepository $phoneRepository, Request $request): Response
    {
        try {
            $phones = $phoneRepository->findPhones($request);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 400 . ": Bad Request",
                'message' => $e->getMessage()
            ], 
            400);
        }
        
        if ($phones == []) {
            return $this->json([
                'status' => 200 . ": Success",
                'message' => "Aucun résultat pour cette requête."
            ],
            200);
        }

        return $this->json($phones, 200, [],[
                'groups' => 'list_phones'
            ]
        );
    }

    /**
     * @Route("/phone/{id}", name="show_phone", methods={"GET"})
     */
    public function showPhone(FormatResponse $formatResponse, NormalizerInterface $normalizer, Phone $phone = null): Response 
    {
        if ($phone == null) {
            return $this->json([
                'status' => 404 . ": Page not Found",
                'message' => "Cette ressource n'existe pas."
            ], 404);
        }

        $phoneNormalize = $normalizer->normalize($phone, null, ['groups' => 'show_phone']);
        
        $phoneFormated = $formatResponse->format($phoneNormalize);  

        return $this->json($phoneFormated, 200);
    }

    /**
     * @Route("/phones/filter", name="filter_phones", methods={"GET"})
     */
    /*public function filterPhones(PhoneFilterRepository $phoneFilterRepository, Request $request): Response
    {
        try {
            $phones = $phoneFilterRepository->filter($request);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 400 . ": Bad Request",
                'message' => $e->getMessage()
            ], 
            400);
        }
        
        if ($phones == []) {
            return $this->json([
                'status' => 200 . ": Success",
                'message' => "Aucun résultat pour cette requête."
            ],
            200);
        }

        return $this->json($phones, 200, [],[
                'groups' => 'list_phones'
            ]
        );
    }*/
}
