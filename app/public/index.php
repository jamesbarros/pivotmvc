<?php

use ICanBoogie\Inflector;

require __DIR__ . '/../bootstrap.php';

header('Content-Type: application/json');

// routing: /:env/:endpoint/:id
$uri = explode("/", substr($_SERVER['REQUEST_URI'],1));
$call['env'] = isset($uri[0]) ? $uri[0] : null;
$call['endpoint'] = isset($uri[1]) ? $uri[1] : null;
$call['id'] = isset($uri[2]) ? $uri[2] : null;

// what is our db stuff gonna be? (we should do this in the model, so we don't get fucked like FAPI)
// we should determine which controller we're calling here. 
switch($_SERVER['REQUEST_METHOD'])
{
    // these will make calls to whatever controller they should be hitting. 
    case 'GET':
     //   echo 'getting';
        $result = get($call['env'], $call['endpoint'], $call['id']);
        break; 
    case 'PUT':
     //   echo 'putting';
        $result = put($call['env'], $call['endpoint'], $call['id']); 
        break;
    case 'POST':
        echo 'posting'; 
        break;
    case 'DELETE':
        //echo 'deleting';
        $result = delete($call['env'], $call['endpoint'], $call['id']); 
        break;
    
}

$return = json_encode($result);
echo $return; 
exit; 

function getModel($env, $endpoint)
{
    // This needs to be generalized so that userland consumer can specify his namespace
    // prefix, or a list of prefixes, or a callback that constructs the model name
    $modelClass = sprintf('\PivotMvc\Model\%sModel', Inflector::get()->camelize($endpoint));
    if (!class_exists($modelClass)) {
        $modelClass = '\PivotMvc\Model\DefaultModel';
    }
    
    // We should inject the connection here rather than instantiating the connection
    // in the model. Makes the models easier to test, and keeps the application config 
    // information higher in the application stack.
    // @todo: need to access config here. 
    $connection = new MongoClient();
    $model = new $modelClass($connection);
    return $model;
}

function put($env, $endpoint, $id)
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
     
    $result = getModel($env, $endpoint)->save($env, $endpoint, $id, $data); 
    return $result;
}

function get($env, $endpoint, $id)
{
    $model = getModel($env, $endpoint);
    $result = $model->find($env, $endpoint, $id);
    return $result;
}

function delete($env, $endpoint, $id)
{
    $result = getModel($env, $endpoint)->remove($env, $endpoint, $id);
    return $result;
}

