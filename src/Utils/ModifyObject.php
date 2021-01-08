<?php

namespace App\Utils;

use App\Entity\User;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

/**
 * This class allows modify an existing object in database. 
 * It make the difference between the method 'PUT' and the method 'PATCH'
 */
class ModifyObject
{    
    /**
     * requestStack
     *
     * @var RequestStack
     */
    private $requestStack;
    
    /**
     * dataPutControl
     *
     * @var DataControl
     */
    private $dataControl;
    
    public function __construct(EntityManagerInterface $manager, 
        RequestStack $requestStack, DataControl $dataControl
    ) {
        $this->manager = $manager;
        $this->requestStack = $requestStack;
        $this->dataControl = $dataControl;
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
        
        //We decode json for get an array
        $data = json_decode($json, true);
        
        if ($data == null) {
            throw new NotEncodableValueException("Le format de données n'est pas conforme. Impossible de décoder le json.");
        }
        
        //If method is PUT, we check all entries for update the ressource
        if ($verbHttp == 'PUT') {
            
            $className = substr(get_class($initial), 11);
            
            $method = strtolower($className) . 'Control';
            
            if (method_exists($this->dataControl, $method)) {
                $data = $this->dataControl->$method($data);
            }
        }
        
        //We loop on $data for update the ressource
        foreach ($data as $key => $element) {

            $method = 'set' . ucfirst($key);

            if (method_exists($initial, $method)) {
                $initial->$method($element);
            }
        }
        return $initial;
    }
}