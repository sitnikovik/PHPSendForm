<?php
/*
    YOUR ROUTES
    The name of method is the same as GET parametr
*/
class PHPSendFormRouter extends PHPSendFormController
{

    static function main()
    {
        $post = self::POST(["name","email"]);
        if (empty($post) || !empty($post["error"])) return $post;

        $send = self::settings([
            "to"=>$post["email"]
        ]);
        $data = array_merge($post, $send);
        $keys = [];
        foreach ($data as $key => $value) $keys[] = "{{$key}}"; 
        $send["text"] = str_replace($keys, $data, file_get_contents($_SERVER["DOCUMENT_ROOT"].'/order.html'));
        $form = self::connect();
        $ok = $form->send($send);
        if ($ok !== true) return ["error"=>$ok];

        return ["success"=>self::$response];
    }


}

?>