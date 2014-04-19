<?


// raw test of various parts all crammed into one file. 
// will break this out into appropriate places shortly. 
// James Barros <james.a.barros@g-don't-spam-me-mail.com> 2014-04-19

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
   $result = save($env, $endpoint, $id, $data); 
    
    return $result; 
    
}

function get($env, $endpoint, $id)
{
    $result = find($env, $endpoint, $id); 

    return $result;
}

function delete($env, $endpoint, $id)
{
    $result = remove($env, $endpoint, $id); 

    return $result;
}


function remove($env, $endpoint,$id)
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
    
   try { 
        $connection = new MongoClient();
        if (!$connection)
            throw new ErrorException('Could not connect to Mongo');
        
        $collection = $connection->$env->$endpoint;
        if (!isset($collection))
            throw new ErrorException('Could not get collection'.$env.'->'.$endpoint);
            
        $collection->remove($options);
    
        $data = "Deleted ".$id;         
        //var_dump($data);
    }
    catch (exception $e)
    {
        return $e->getMessage();
    }
    
    return $data; 
    
}



function find($env, $endpoint, $id)
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
    
     
   try { 
        $connection = new MongoClient();
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
        
        //var_dump($data);
    }
    catch (exception $e)
    {
        return $e->getMessage();
    }
    
    return $data; 
    
}


function save($env, $endpoint, $id, $data)
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
        $connection = new MongoClient();
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
   
   
} 

?> 
