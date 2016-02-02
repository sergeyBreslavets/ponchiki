<?php
require_once('/mail.php');
//require_once('/mamp/htmlhouse/d1gitable/public/mail.php');
/**
*  Request
*/
class Request {
  public $get = array();
  public $post = array();
  public $cookie = array();
  public $files = array();
  public $server = array();

  public function __construct() {
    $_GET = $this->clean($_GET);
    $_POST = $this->clean($_POST);
    $_REQUEST = $this->clean($_REQUEST);
    $_COOKIE = $this->clean($_COOKIE);
    $_FILES = $this->clean($_FILES);
    $_SERVER = $this->clean($_SERVER);

    $this->get = $_GET;
    $this->post = $_POST;
    $this->request = $_REQUEST;
    $this->cookie = $_COOKIE;
    $this->files = $_FILES;
    $this->server = $_SERVER;
  }

  public function clean($data) {
    if (is_array($data)) {
      foreach ($data as $key => $value) {
        unset($data[$key]);

        $data[$this->clean($key)] = $this->clean($value);
      }
    } else { 
      $data = htmlspecialchars($data, ENT_COMPAT, 'UTF-8');
    }

    return $data;
  }
}
/**
*  FormHandler
*/
class FormHandler 
{
  private $to;
  private $subject;
  private $error = array();
  private $request;
  function __construct($request)
  {
     $this->to = 'venom.bren@gmail.com';
     // 'yuri.breslavets@d1gitable.ru';
    $this->subject = 'Сообщение с сайта ponchiki';
    $this->request = $request;
  }
  function sendMessage(){
    $json = array();
    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

        $mail = new Mail();
         $mail->protocol ='mail';
 


 
        $mail->setTo($this->to);
        $mail->setFrom($this->request->post['c_email']);
        $mail->setSender($this->request->post['c_name']);
        $mail->setSubject(html_entity_decode($this->subject, ENT_QUOTES, 'UTF-8'));

       $con = "Name: "     . $this->request->post["c_name"]    . "\r\n";
       $con .= "Email: "    . $this->request->post["c_email"]   . "\r\n";
       $con .= "Message: "  . "\r\n" . $this->request->post["c_message"];

        $mail->setText(html_entity_decode($con, ENT_QUOTES, 'UTF-8'));
        $mail->send();



      $json['success'] = 1;



    }else {
      $json['error'] = $this->error;
    }
    echo json_encode($json);
  }
  protected function utf8_strlen($string) {
    return strlen(utf8_decode($string));
  }
  protected function validateForm() {

    if (($this->utf8_strlen($this->request->post['c_name']) < 1) || ($this->utf8_strlen($this->request->post['c_name']) > 96)) {
        $this->error['c_name'] = 'Please fill your name.';
    }
    if (($this->utf8_strlen($this->request->post['c_email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['c_email'])) {
        $this->error['c_email'] = 'Please fill a valid e-mail.';
    }
    if (($this->utf8_strlen($this->request->post['c_message']) < 5)) {
        $this->error['c_message'] = 'Please fill a message.';
    }
    if (!$this->error) {
      return true;
    } else {
      return false;
    }
  }
}
  $request = new Request();
  $message = new FormHandler($request);
  $message ->sendMessage();

?>

