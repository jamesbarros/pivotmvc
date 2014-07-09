<?php

/*
 * baseModel provides underlying CRUD functions and is extended by usable models.
 * Author: James Barros <james.a.barros@gmail.com>
 * 
 */

namespace pivot\model;

abstract class baseModel
{
   
    public function save($env, $endpoint, $id, $data)
    {
        // move this to a config file, and grab using scope.
        // username and password? 
        $dbConnection = array
            ('server' => 'localhost',
            'port' => 27017,
             'db' => $env
            );  
   
        $data->_id = $id;
   
        try { 
            $connection = new \MongoClient();
            if (!$connection)
                throw new ErrorException('Could not connect to Mongo');
            
            $collection = $connection->$env->$endpoint;
            if (!isset($collection))
                throw new ErrorException('Could not get collection'.$env.'->'.$endpoint);
            
            $collection->update(array('_id'=>$id), $data, array('upsert' => true));
        }
        
        catch (exception $e)
        {
            return $e->getMessage();
        }
    
        return $data;
    
    } // end save
    
    
    public function remove($env, $endpoint,$id)
    {
        // move this to a config file, and grab using scope.
        // username and password? 
        $dbConnection = array
            ('server' => 'localhost',
            'port' => 27017,
             'db' => $env
            );  
    
    
        $options = array(); 
        if (isset($id) && $id)
            $options['_id'] = $id;
        else
            return (array("error" => "must specify an id to remove records")); 
    
        try
        { 
            $connection = new \MongoClient();
            if (!$connection)
                throw new ErrorException('Could not connect to Mongo');
        
            $collection = $connection->$env->$endpoint;
            
            if (!isset($collection))
                throw new ErrorException('Could not get collection'.$env.'->'.$endpoint);
            
            $collection->remove($options);
    
            $data = "Deleted ".$id;         
        }
        
        catch (exception $e)
        {
            return $e->getMessage();
        }
    
    
        return $data; 
    }


    public function find($env, $endpoint, $id)
    {
        // move this to a config file, and grab using scope.
        // username and password? 
        $dbConnection = array
            ('server' => 'localhost',
            'port' => 27017,
             'db' => $env
            );  
    
        $options = array(); 
        if (isset($id) && $id)
            $options['_id'] = $id; 
    
     
        try
        { 
            $connection = new \MongoClient();
            if (!$connection)
                throw new ErrorException('Could not connect to Mongo');
        
            $collection = $connection->$env->$endpoint;
            if (!isset($collection))
                throw new ErrorException('Could not get collection'.$env.'->'.$endpoint);
            
            $cursor = $collection->find($options);
            foreach($cursor as $doc)
                $data[] = $doc;
            
            if (!isset($data) || !is_array($data))
                $data = "No Data Found.";
        
        }
        catch (exception $e)
        {
            return $e->getMessage();
        }
    
    return $data; 
    
    }
    
}
