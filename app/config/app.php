<?php

// wrapping this in a call_user_func() call allows us to use local variables 
// inside, while keepng the global namespace unpolluted
return call_user_func(function(){
    
    return array(
        
        // db config
        'db' => array(
            'host' => '127.0.0.1',
        ),
        
        // Moar config here!
    );
});
