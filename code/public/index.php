<?


require('../model/default.model.php');
use pivot/model/default as model;


header('Content-Type: application/json');

$uri = explode("/", substr($_SERVER['REQUEST_URI'],1));

if (isset($uri[0]))
    $call['env'] = $uri[0];
else
    $call['env'] = NULL;

if (isset($uri[1]))
    $call['endpoint'] = $uri[1];
else
    $call['endpoint'] = NULL;
    
if (isset($uri[2]))
    $call['id'] = $uri[2];
else
    $call['id'] = NULL;
    
    

// lets build a router.
// what controller do we call.


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

function put($env, $endpoint, $id)
{
    
 
    $body = file_get_contents('php://input');
    
    try
    {
        if (!$data = json_decode($body))
        { 
          throw new exception('invalid json');
          //die ('invalid json 1');
        }
    }
    catch (exception $e)
    {
        return $e->getMessage();
    }
     
   // echo "body is : ",$body;
   // var_dump($data);
   $model = new model();
   $result = $model->save($env, $endpoint, $id, $data); 
    
    return $result; 
    
}

function get($env, $endpoint, $id)
{
    $model = new model();
    $result = $model->find($env, $endpoint, $id); 

    return $result;
}

function delete($env, $endpoint, $id)
{
    $model = new model();
    $result = $model->remove($env, $endpoint, $id); 

    return $result;
}



?> 
