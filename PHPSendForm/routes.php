<?php
/*
   YOUR ROUTES
    The name of method is the same as GET parametr
*/
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

?>