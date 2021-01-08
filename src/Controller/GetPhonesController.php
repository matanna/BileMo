<?php

namespace App\Controller;

use App\Entity\Phone;

use App\Response\FormatResponse;
use App\Repository\PhoneRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class GetPhonesController extends AbstractController
{
    /**
     * @Route("/phones", name="list_phones", methods={"GET"})
     * 
     * 
     */
    public function getPhones(PhoneRepository $phoneRepository, Request $request,
        CacheInterface $cache
    ): Response {

        try {

            //The cache feature is disabled because we work with a pgination feature

            //$phones = $cache->get('item_phones', function(ItemInterface $item) use ($phoneRepository, $request){
                //$item->expiresAfter(3600);

                $phones = $phoneRepository->findPhones($request);

                //return $phones;
            //});
            
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
     * @Route("/phones/{id}", name="show_phones", methods={"GET"})
     */
    public function showPhone(FormatResponse $formatResponse, NormalizerInterface $normalizer, Phone $phone): Response 
    {
        try {
            $phoneNormalize = $normalizer->normalize($phone, null, ['groups' => 'show_phone']);

        } catch (NotEncodableValueException $e) {
            return $this->json([
                'status' => 400 . ': Bad Request',
                'message' => $e->getMessage()
            ], 400);
        }
        
        $phoneFormated = $formatResponse->format($phoneNormalize);  

        return $this->json($phoneFormated, 200);
    }
}
