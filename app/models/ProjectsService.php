<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProjectsService
 *
 * @author Rob
 */
class ProjectsService implements ProjectServiceInterface
{
    public function fetchAll()
    {
        $raw = DB::select('select * from projects');
        $objects = $this->_makeObjects($raw);
        
        return $json;
    }
    
    public function fetch($query)
    {
        return 'a single thing';
    }
    
    protected function _makeObjects($raw)
    {
        $objects = array();
        
        return $objects;
    }
}