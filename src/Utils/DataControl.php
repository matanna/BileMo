<?php

namespace App\Utils;

use Symfony\Component\Serializer\Exception\NotEncodableValueException;

/**
 * This class allows to control the body of request when it use PUT method
 */
class DataControl
{
    private $data;
    
    private $validKey;

    /**
     * Method userPutControl
     * It control entries for update a User
     *
     * @param $data 
     *
     * @return array
     */
    public function userControl($data)
    {
        if ($data == null) {
            throw new NotEncodableValueException("Erreur de syntaxe dans les données json.");
        }
        $this->data = $data;

        $validKey = ['username', 'email', 'tel', 'profilPicture', 'adress'];
        $this->validKey = $validKey;

        $this->control();

        $data['dateAtCreated'] = new \Datetime();

        return $data;
    }

    private function control()
    {
        foreach ($this->validKey as $key) {
            if (!array_key_exists($key, $this->data)) {
                throw new NotEncodableValueException("La clé $key est manquante dans le body, la ressource doit être complète.");
            } 
        }
        
    }
}