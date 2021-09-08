<?php

class PHPSendFormController 
{

    static $error = array(
        "500"=>"Something was wrong. Retry again.",
        "502"=>"BAD GATEWAY",
        "503"=>"SESSION INCORRUPTED",
    );
    static $response = "Form was sent successfully";
    
    // get data from POST and require 
    static function POST($required = [])
    {
        if (empty($_POST)) return ["error"=>"The form is empty"];
        $post = $_POST;
        if (empty($post["csrf"]) || empty(trim($post["csrf"]))) return ["error"=>self::$response]; // trick for robots
        unset($post["csrf"]);
        if (!empty($required)) 
        {
            foreach ($required as $i => $key) {
                if (empty($post[$key]) || empty(trim($post[$key]))) return ["error" => "There are some inputs not filled"];
                switch (trim($key)) {
                    case "email":
                        if (!empty($post["email"]) && !filter_var($post["email"], FILTER_VALIDATE_EMAIL)) return ["error" => "Incorrect email"];
                    break;
                    default:
                        if (empty($post[$key])) return ["error"=>"Enter $key"];
                    break;
                }
            } 
        }

        // uploaded files
        $files = [];
        if (!empty($post["uploaded-files"])) {
            foreach (json_decode($post["uploaded-files"],true) as $key => $file) 
            {
                // Sometimes, file do not sent in email file data. Therefore I used to put it in email text to exclude this moment
                if (!empty($file["filepath"])) $files[] = '<a href="http://'.$_SERVER["HTTP_HOST"].'/'.trim($file["filepath"]).'" target="blank">FIle №'.($key+1).'</a>'; 
            }
            unset($post["uploaded-files"]);
        }
        $post["files"] = (!empty($files)) ? implode("<br>",$files) : "";

        return $post;
    }

    // sets form
    static function settings($array)
    {
        $default = [
            "subject"=>"NEW ORDER",
            "to"=>"", // куда отправляем заявки
            "to_name"=>"",
            "host"=>$_SERVER["HTTP_HOST"],
        ];
        return array_merge($default,$array);
    }

    // connection to smtp server and creating Form model
    static function connect()
    {
        if (!class_exists('PHPSendForm')) return false;
        if (!file_exists(__DIR__.'/config.json')) return false;
        $config = json_decode(__DIR__.'/config.json',true);
        return new PHPSendForm([
            'smtp_secure'=> $config["smtp_secure"],
            'smtp_host'=> $config["smtp_host"],
            'smtp_port'=> $config["smtp_port"],
            'smtp_username'=> $config["smtp_username"],
            'smtp_password'=> $config["smtp_password"],
            'mail_from'=> $config["mail_from"], //Адрес отправителя
            'mail_from_name'=> $config["mail_from_name"], //Имя отправителя (Опционально)
        ]);
    }

}

?>