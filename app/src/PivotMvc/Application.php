<?php

namespace PivotMvc;

use ICanBoogie\Inflector;

/**
 * Main application
 */
class Application
{

    protected $env;
    protected $config;
    protected $connection;

    /**
     * Constructor
     * 
     * @param array $config
     */
    public function __construct($env, $config)
    {
        $this->env = $env;
        $this->config = $config;
    }

    /**
     * Public entry point
     */
    public function run()
    {
        // routing: /:env/:endpoint/:id
        $uri = explode('/', substr($_SERVER['REQUEST_URI'], 1));
        $call['space'] = isset($uri[0]) ? $uri[0] : null;
        $call['endpoint'] = isset($uri[1]) ? $uri[1] : null;
        $call['id'] = isset($uri[2]) ? $uri[2] : null;

        // At present, there are no controllers, so we have all these HTTP methods 
        // handled here at the application level, dealing directly with models
        // here. 
        // @todo: add controllers, probably want to feed models into controllers
        // @todo: more checks, exception handling etc.
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        $result = $this->$method($call['space'], $call['endpoint'], $call['id']);
        
        // output 
        // @todo: HTTP status codes
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    /**
     * Build a db connection
     * 
     * @return \MongoClient
     */
    protected function buildConnection()
    {
        $host = !empty($this->config['db']['host']) ? $this->config['db']['host'] : 'localhost';
        $connection = new \MongoClient($host);
        return $connection;
    }
    
    /**
     * Get the db connection
     * 
     * @return \MongoClient
     */
    public function getConnection()
    {
        if (null === $this->connection) {
            $this->connection = $this->buildConnection();
        }
        return $this->connection;
    }

    /**
     * Build a model for a given request
     * 
     * @param string $space
     * @param string $endpoint
     * @return \PivotMvc\Model\BaseModel
     */
    protected function buildModel($space, $endpoint)
    {
        $connection = $this->getConnection();
        
        // This needs to be generalized so that userland consumer can specify his namespace
        // prefix, or a list of prefixes, or a callback that constructs the model name
        $modelClass = sprintf('\PivotMvc\Model\%sModel', Inflector::get()->camelize($endpoint));
        if (!class_exists($modelClass)) {
            $modelClass = '\PivotMvc\Model\DefaultModel';
        }

        $model = new $modelClass($connection);
        return $model;
    }

    /**
     * Handle HTTP PUT request
     * 
     * @todo: move out to controller
     * 
     * @param string $space
     * @param string $endpoint
     * @param string $id
     * @return array
     * @throws \Exception
     */
    protected function put($space, $endpoint, $id)
    {
        $body = file_get_contents('php://input');

        try {
            if (!$data = json_decode($body)) {
                throw new \Exception('Invalid json');
                //die ('invalid json 1');
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        $result = $this->buildModel($space, $endpoint)->save($space, $endpoint, $id, $data);
        return $result;
    }

    /**
     * Handle HTTP GET request
     * 
     * @todo: move out to controller
     * 
     * @param string $space
     * @param string $endpoint
     * @param string $id
     * @return array
     */
    protected function get($space, $endpoint, $id)
    {
        $model = $this->buildModel($space, $endpoint);
        $result = $model->find($space, $endpoint, $id);
        return $result;
    }

    /**
     * Handle HTTP DELETE request
     * 
     * @todo: move out to controller
     * 
     * @param string $space
     * @param string $endpoint
     * @param string $id
     * @return array
     */
    protected function delete($space, $endpoint, $id)
    {
        $result = $this->buildModel($space, $endpoint)->remove($space, $endpoint, $id);
        return $result;
    }
    
    /**
     * Handle HTTP POST request
     * 
     * @todo: move out to controller
     * 
     * @param string $space
     * @param string $endpoint
     * @param string $id
     * @return array
     */
    protected function post($space, $endpoint, $id)
    {
        throw new \RuntimeException('Not yet implemented');
    }

}
