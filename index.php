<?php

    require_once __DIR__.'/PHPSendForm/autoloader.php';
    // Form event
    if (isset($_GET["form"])) 
    { 
        // var form from GET is the name of event listener in Controller. You may name it howevere you want)
        $method = trim($_GET["form"]);
        if (method_exists("PHPSendFormRouter",$method)) die(json_encode(PHPSendFormRouter::$method())); 
        else die(json_encode(["error"=>"There is not registred form listener"]));
    }


    include __DIR__ . '/index.html';
?>