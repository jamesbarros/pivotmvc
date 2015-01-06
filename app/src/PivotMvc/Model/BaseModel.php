<?php

/*
 * baseModel provides underlying CRUD functions and is extended by usable models.
 * Author: James Barros <james.a.barros@gmail.com>
 * 
 */
namespace PivotMvc\Model;

abstract class BaseModel
{

    /**
     * @var \MongoClient
     */
    protected $connection;
    
    /**
     * Constructor
     * 
     * @param \MongoClient $connection
     */
    public function __construct(\MongoClient $connection)
    {
        $this->connection = $connection;
    }
    
    public function save($env, $endpoint, $id, $data)
    {
        $data->_id = $id;
   
        try {
            $collection = $this->connection->$env->$endpoint;
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
        $options = array(); 
        if (isset($id) && $id)
            $options['_id'] = $id;
        else
            return (array("error" => "must specify an id to remove records")); 
    
        try
        { 
            $collection = $this->connection->$env->$endpoint;
            
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
        try { 
            $collection = $this->connection->$env->$endpoint;
            if (!$collection) {
                throw new \RuntimeException('Could not get collection'.$env.'->'.$endpoint);
            }
            
            $query = array(); 
            if ($id) {
                $query['_id'] = $id;
            }
            $cursor = $collection->find($query);
            $data = array();
            foreach ($cursor as $doc) {
                $data[] = $doc;
            }
            
            if (!$data || !is_array($data)) {
                $data = [];
            }
        
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    
        return $data; 
    
    }
    
}
