<?php

namespace App\Response;

/**
 * This class format data for display a good response in json
 */
class FormatResponse
{    
    /**
     * Method format
     *
     * @param $data $data
     * @param $metadata $metadata
     *
     * @return void
     */
    public function format($data, $metadata)
    {
        $newData = [];

        foreach($data as $key => $element) {
            $newData['item n°' . $key] = $element; 
        }
        $response['data'] = $newData;

        $response['metadata'] = $metadata;

        return $response;
    }
}