<?php

namespace App\Controller;

use App\Entity\Phone;

use App\Response\FormatResponse;
use App\Repository\PhoneRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

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
class GetPhonesController extends AbstractController
{
    /**
     * 
     * @Route("/phones", name="list_phones", methods={"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns the list of phones with pagination system",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Phone::class, groups={"list_phones"}))
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
     *     name="brand",
     *     in="query",
     *     description="Filter results by brand",
     *     @OA\Schema(type="string", example="#/phones?brand=samsung")
     * )
     * @OA\Parameter(
     *     name="avaibale",
     *     in="query",
     *     description="Filter results by availability",
     *     @OA\Schema(type="{0, 1}", example="#/phones?avaibale=1")
     * )
     * @OA\Parameter(
     *     name="minprice",
     *     in="query",
     *     description="Get results greater than a number",
     *     @OA\Schema(type="integer", example="#/phones?minprice=700")
     * )
     * @OA\Parameter(
     *     name="maxprice",
     *     in="query",
     *     description="Get results inferior than a number",
     *     @OA\Schema(type="integer", example="#/phones?maxprice=1200")
     * )
     * @OA\Parameter(
     *     name="byprice",
     *     in="query",
     *     description="Try results by price",
     *     @OA\Schema(type="{ASC, DESC}", example="#/phones?byprice=DESC")
     * )
     * @OA\Parameter(
     *     name="search",
     *     in="query",
     *     description="Search a phone by model with a string of characters",
     *     @OA\Schema(type="string", example="#/phones?search=foo")
     * )
     * @OA\Parameter(
     *     name="perpage",
     *     in="query",
     *     description="Define the number of element per page - default = 10",
     *     @OA\Schema(type="integer", example="#/phones?perpage=8")
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="Define the page - default = 1",
     *     @OA\Schema(type="integer", example="#/phones?page=2")
     * )
     * @OA\Tag(name="phones")
     * @Security(name="Bearer")
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
                'status' => 200 . ": No Content",
                'message' => "Aucun résultat pour cette requête."
            ],
            204);
        }

        return $this->json($phones, 200, [],[
                'groups' => 'list_phones'
            ]
        );
    }

    /**
     * @Route("/phones/{id}", name="show_phones", methods={"GET"})
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns details of one phone",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Phone::class, groups={"show_phone"}))
     *        
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
     *     description="The id of one phone",
     *     @OA\Schema(type="integer", example="#/phones/123")
     * )
     * @OA\Tag(name="phones")
     * @Security(name="Bearer")
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
