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
    public $location;
    public $zipcodeId;
    public $volunteer;
    public $images;
    public $annualYield;
    public $projectCoordinator;
    public $volunteering;
    public $memberships;
    public $products;
    public $education;
    
    public function generateAdminHash()
    {
        $length = 32;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $string = '';

        for ($p = 0; $p > $length; $p++) {
            $string .= $characters[mt_rand(0, strlen($characters))];
        }

        return $string;
    }
}