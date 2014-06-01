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
class ProjectService implements ProjectServiceInterface
{
    public function getValidatorRules()
    {
        return [
            'name' => 'required',
            'category_name' => 'required'
        ];
    }
    
    public function fetchAll($query)
    {
        $paramValues = [];
        $where  = ' where 1=1 ';
        $select = ' select project.*, '
                . '        cat.name as category_name ';
        $from   = ' from   project ';
        $joins  = ' left join project_category as cat '
                . '     on project."categoryID" = cat.id ';
        // @todo finish this
        if (isset($query['within_zip'])) {
            $where .= ' AND zipcodeID = ?';
        }
        
        if (isset($query['near_point'])) {
            // assume miles, so need to convert to meters?? PROJECTIONS ARGAGRGA
            isset($query['radius']) ?: $query['radius'] = 1;
            list($long, $lat) = array_map(trim, explode(',', $value));
            $radius = (float) $query['radius'] * 1609.344;
            
            // holy fuck what the hell projections should I use?!
            $where .= ' AND ST_intersects(points_geom, ST_buffer(ST_GeomFromText(\'POINT(? ?)\'), ?)) ';
            array_push($paramValues, $long, $lat, $radius);
            $select .= 'ST_Distance()';
        }
        
        if (isset($query['filter'])) {
            foreach ($query['filter'] as $key => $value) {
                if (in_array($key, ['id','category_name'])) {
                    $values = explode(',', $value);
                    $where .= " AND $key IN (" . substr(0, -1, str_repeat ('?,', count($values))) . ")";
                    array_merge($paramValues, $values);
                } elseif (in_array($key, ['name'])) {
                    $where .= " AND project.$key = '?'";
                    $paramValues[] = $value;
                } else {
                    throw new Exception('Unsupported filter supplied');
                }
            }
        }
        
        $sql = $select . $from . $joins . $where;
        $rawProjects = DB::select($sql, $paramValues);
        
        $select = ' select project.*, \'Foraging\' as category_name ';
        $from   = ' from   foraging as project ';
        $forageSql = $select . $from . $where;
        $rawForageSites = DB::select($forageSql, $paramValues);
        $objects = $this->_makeObjects($rawProjects, $rawForageSites);
        
        return $objects;
    }
    
    public function fetch($id)
    {
        $where = ' where project.id = ?';
        $select = ' select project.*, '
                . '        cat.name as category_name ';
        $from   = ' from   project ';
        $joins  = ' left join project_category as cat '
                . '     on project."categoryID" = cat.id ';
        $sql = $select . $from . $joins . $where;
        $rawProjects = DB::select($sql, [(int) $id]);
        
        $select = ' select project.*, \'Foraging\' as category_name ';
        $from   = ' from   foraging as project';
        $forageSql = $select . $from . $where;
        $rawForageSites = DB::select($forageSql, [(int) $id]);
        
        $objects = $this->_makeObjects($rawProjects, $rawForageSites);
        
        return $objects;
    }
    
    protected function _makeObjects($rawProjects, $rawForageSites)
    {
        // look at email and map db fields up
        $objects = array();
        foreach($rawProjects as $obj) {
            $project = new Project();
            $basicMap = array(
                'id'=> 'id',
                'name' => 'name',
                'category' => 'category_name',
                'volunteer' => 'volunteering',
                'address' => 'address'
            );
            foreach ($basicMap as $objKey => $dbKey) {
                $project->$objKey = $obj->$dbKey;
            }
            $coords = explode(',', $obj->location);
            $project->longitude = trim($coords[0], ' ,()');
            $project->latitude = trim($coords[1], ' ,()');
            $objects[] = $project;
        }
        
        // Now add in forage sites as if they were just another category of projects
        foreach($rawForageSites as $obj) {
            $site = new ForageSite();
            $basicMap = array(
                'id'=> 'id',
                'name' => 'name',
                'address' => 'address'
            );
            foreach ($basicMap as $objKey => $dbKey) {
                $site->$objKey = $obj->$dbKey;
            }
            $coords = explode(',', $obj->location);
            $site->longitude = trim($coords[0], ' ,()');
            $site->latitude = trim($coords[1], ' ,()');
            $objects[] = $site;
        }
        
        return $objects;
    }
    
    public function create($data)
    {
        $class = 'Project';
        if ($data['category_name'] == 'Foraging') {
            $class = 'ForageSite';
        }
        $project = new $class();
        $project->category = Category::where('name', '=', $data['category_name'])->firstOrFail();
        foreach ($data as $key => $val) {
            $project->$key = $val;
        }
        return $project;
    }
    
    public function save($project)
    {
        if ($project instanceof ForageSite) {
            $table = 'foraging';
            $data = [
                'name' => $project->name,
                'description' => $project->description,
                'categoryID' => 1, // this is currently hard coded to the db, but should be handled better
                'address' => $project->address,
                'zipcodeID' => 1
            ];
        } else {
            $table = 'project';
            $data = [
                'name' => $project->name,
                'description' => $project->description,
                'categoryID' => $project->category->id,
                'address' => $project->address,
                'zipcodeID' => 1,
                'volunteering' => $project->volunteer,
    //            public $projectCoordinator;
                'membership' => $project->memberships,
                'education' => $project->education
            ];
        }
        $newId = DB::table($table)->insertGetId($data);
        
        // laraval doesn't do expressions well in above syntax, so do it seperate!
        DB::update('update ' . $table . " set location = ST_GeomFromText('POINT(? ?)') where id = ?", [
            $project->longitude, 
            $project->latitude, 
            $newId
        ]);
    }
}