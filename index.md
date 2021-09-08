### Get started

1. Download the package in your website folder and include just one file like `/path/to/PHPSendForm/autoload.php`

2. Change data in config.json. There are settings to connect SMTP server

3. Then you need to initialize the event listener in index.php or another file where you want to send form request. 

```markfown

   // include class loader
   require_once __DIR__.'/PHPSendForm/autoloader.php';
   
  // Form event
  if (isset($_GET["form"])) 
  { 
    // var form from GET is the name of event listener in Controller. You may name it howevere you want)
    $method = trim($_GET["form"]);
    if (method_exists("PHPSendFormRouter",$method)) die(json_encode(PHPSendFormRouter::$method())); 
    else die(json_encode(["error"=>"There is not registred form listener"]));
  }
    
```

4. Ok! Now you have to register form route in # PHPSendFormRouter in `routes.php`. This is the class extended the PHPSendFormController created only for registrating form routes. Other opertations coded in Controller. For example, we have form route `/?form=main`. Router method will have the same name with GET parametr.

```markfown

class PHPSendFormRouter extends PHPSendFormController
{
    
    static function main()
    {
        $post = self::POST(["name","email"]); // the argument - required form data. if input is empty - there will be error
        if (empty($post) || !empty($post["error"])) return $post;

        // OK. Setup the sending data. settings() merge defaults with argument.
        $send = self::settings([
            "to"=>$post["email"] // send message to this email
        ]);
        $data = array_merge($post, $send); // merge POST data width sending setup
        
        // ---
        // Custom. If we have to send HTML message, we should replace content in HTML template. This code point may be customized with you prefeures.
        $keys = [];
        foreach ($data as $key => $value) $keys[] = "{{$key}}"; 
        $send["text"] = str_replace($keys, $data, file_get_contents($_SERVER["DOCUMENT_ROOT"].'/order.html'));
        --- //
        
        $form = self::connect(); // Connect the SMTP Server. WARNING! Because of SSL, there are may be some errors with local and prod builds/
        $ok = $form->send($send); // sending the message. Returns true if OK, or error text
        if ($ok !== true) return ["error"=>$ok];
        
        return ["success"=>self::$response]; 
        // if OK return the success response with your listener in JavaScript. 
        // I prefeure this way like array, where key is type of response and value is the value of response ))
    }
    
    // There will be next route
} 

```


## Notes

In package I used JQuery for simple Ajax requests but you can use it with native JavaScript or another framework.


### Support or Contact
If you need a help with the plugin you can message me on VK or Instagram.
https://vk.com/sitnikovik
https://www.instagram.com/sitnikovik

### Enjoy!

