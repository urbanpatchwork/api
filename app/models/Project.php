<?php
/**
 * Description of Project
 *
 * @author Rob
 */
class Project {
    public $id;
    public $name;
    public $category;
    //"keywordID" integer NOT NULL,
    public $description;
    public $address;
    public $zipcodeId;
    public $volunteer;
    public $images;
    public $annualYield;
    public $projectCoordinator;
    public $memberships;
    public $products;
    public $education;
    public $longitude;
    public $latitude;
    
    public function generateAdminHash()
    {
        $length = 32;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $string = '';

        for ($p = 0; $p < $length; $p++) {
            $string .= $characters[mt_rand(0, strlen($characters))];
        }

        return $string;
    }
}