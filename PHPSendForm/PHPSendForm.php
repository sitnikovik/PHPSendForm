<?php 

class PHPSendForm 
{
    public function __construct($array) 
    {
        $this->smtp = [
            "charset"=>(!empty($array["smtp_charset"])) ? trim($array["smtp_charset"]) : "UTF-8",
            "secure"=>(!empty($array["smtp_secure"])) ? trim($array["smtp_secure"]) : "ssl",
            "host"=>(!empty($array["smtp_host"])) ? trim($array["smtp_host"]) : "",
            "port"=>(!empty($array["smtp_port"])) ? trim($array["smtp_port"]) : "",
            'username'=>(!empty($array["smtp_username"])) ? trim($array["smtp_username"]) : "",
            'password'=> (!empty($array["smtp_password"])) ? trim($array["smtp_password"]) : "",

        ];

        $this->mail = [
            'from'=> (!empty($array["mail_from"])) ? trim($array["mail_from"]) : '', //Адрес отправителя
            'from_name'=> (!empty($array["mail_from_name"])) ? trim($array["mail_from_name"]) : '', //Имя отправителя (Опционально)
            'to'=>(!empty($array["mail_notifications"])) ? trim($array["mail_notifications"]) : "", //Получатель уведомлений из форм заявок
            'to_name'=>(!empty($array["mail_notifications_name"])) ? trim($array["mail_notifications_name"]) : false, //Имя получателя (Опционально)
        ];

    }

    //Функция - отправляет письмо через авторизованный smtp сервер
    public function send($params=array())
    {
        require_once __DIR__.'/PHPMailer-master/PHPMailerAutoload.php';

        $mail             = new PHPMailer();

        $mail->CharSet = $this->smtp["charset"];

        $mail->IsSMTP(); // telling the class to use SMTP
        $mail->SMTPAuth   = true;                  // enable SMTP authentication
        // $mail->SMTPSecure = "tls";
        //$mail->SMTPDebug  = 1;
        $mail->SMTPSecure = $this->smtp["secure"];
        $mail->Host       = $this->smtp["host"];      // SMTP server
        $mail->Port       = $this->smtp["port"];                      // SMTP port
        $mail->Username   = $this->smtp["username"];  // username
        $mail->Password   = $this->smtp["password"];            // password

        $mail->setLanguage('ru', __DIR__ . '/plugins/PHPMailer-master/language/');

        $address=$this->mail['from'];
        $mail->SetFrom($address, $this->mail['from_name']);

        $mail->Subject    = $params['subject'];

        $mail->MsgHTML( $params['text'] );

        $mail->AddAddress($params['to'], $params['to_name']);

        if(isset($params['attachment'])){
            if(!empty($params['attachment'])){
                foreach ($params['attachment'] as $key => $attachment) {
                    if (preg_match("/http/",$attachment)) {  $mail->addAttachment($attachment); }
                    else $mail->addAttachment( DIR.$attachment , basename($attachment)) ;
                }
            }
        }

        $mail->isHTML(true);
        return (!$mail->Send()) ? "Mailer Error: " . $mail->ErrorInfo : true;
    }

}

?>