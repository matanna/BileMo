<?php

namespace App\Utils;

use App\Entity\User;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ModifyObject
{
    private $manager;

    private $requestStack;

    private $normalizer;
    
    public function __construct(EntityManagerInterface $manager, 
        RequestStack $requestStack, ObjectNormalizer $normalizer
    ) {
        $this->manager = $manager;
        $this->requestStack = $requestStack;
        $this->normalizer = $normalizer;
    }

    /**
     * Method update
     *
     * @param $initial 
     * @param $json 
     *
     * @return Entity
     */
    public function update($initial, $json)
    {
        $verbHttp = $this->requestStack->getCurrentRequest()->getmethod();
        
        if ($verbHttp == 'PUT') {
            return $this->putUpdate($initial, $json);
        }

        if ($verbHttp == 'PATCH') {
            return $this->patchUpdate($initial, $json);
        }
     
    }

    private function putUpdate($initial, $json)
    {

        $data = json_decode($json, true);
        $data['id'] = $id;

        $newObject = $this->normalizer->denormalize($data, User::class);

        
        dd($newObject);
        return $newObject;
        
    }

    private function patchUpdate($initial, $json)
    {
        $data = json_decode($json, true);
        
        foreach ($data as $key => $element) {
            $method = 'set' . ucfirst($key);

            if (method_exists($initial, $method)) {
                $initial->$method($element);
            }
        }
        return $initial;
    }
}