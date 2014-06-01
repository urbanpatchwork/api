<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GeoJsonConverter
 *
 * @author Rob
 */
class GeoJsonConverter 
{
    static function convert($array) 
    {
        $converted = [
            'type' => 'FeatureCollection',
            'features' => []
        ];
        foreach ($array as $obj) {
            $thing = [
                'type' => 'Feature',
                'geometry' => ['type' => 'Point', 'coordinates' => [$obj->longitude, $obj->latitude]],
                'properties' => []
            ];
            foreach ($obj as $key => $val) {
                $thing['properties'][$key] = [$val];
            }
            $converted[] = $thing;
        }
        
        return json_encode($converted);
    }
}