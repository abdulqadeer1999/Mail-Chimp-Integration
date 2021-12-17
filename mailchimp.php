<?php
// session_start();
$msg=false;
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$env_key= $_ENV["API_KEY"];
// echo $env_key;
$env_id=  $_ENV["LIST_ID"];


if(isset($_POST['submit'])){
    $name = ($_POST['full_name']);
    $email = ($_POST['email']);
    if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL) === false){
        // MailChimp API credentials
        
        $apiKey = $env_key;
        $listID = $env_id;


        // MailChimp API URL
        $memberID = md5(strtolower($email));
        $dataCenter = substr($apiKey,strpos($apiKey,'-')+1);
        $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listID . '/members/' . $memberID;

        // member information
        $json = json_encode([
            'email_address' => $email,
             'First Name' => $name,
            'status'        => 'subscribed',
            'merge_fields'  => [
                'NAME'     => $name,
            ]
        ]);

        // send a HTTP POST request with curl
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        // echo json_encode($result);

        // store the status message based on response code
        if ($httpCode == 200) {
        //    echo "user added successfully";

           $msg=true;

            // $_SESSION['msg'] = '<p style="color: #34A853">You have successfully subscribed .</p>';
        } 
        // else {
        //     switch ($httpCode) {
        //         case 214:
        //             $msg = 'You are already subscribed.';
        //             break;
        //         default:
        //             $msg = 'Some problem occurred, please try again.';
        //             break;
        //     }
        //     $_SESSION['msg'] = '<p style="color: #EA4335">'.$msg.'</p>';
        // }

        elseif($httpCode == 214) {
            echo "user already exist";
        }else {

            echo "enter your correct email address";
        }

    }
    
    // else{
    //     $_SESSION['msg'] = '<p style="color: #EA4335">Please enter valid email address.</p>';
    // }
}

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

</head>
<body>
<form action="mailchimp.php"  method="post">

  <?php
  
  if($msg==true) {

    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>Success!</strong> You have successfully Subscribed.
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>';
  }

  ?>

    <div class="container">
        <div class="row">
            <div class="col-md-6">

            <h2>Mail Chimp Integration</h2>

    <div class="form-group customised-formgroup"> <span class="icon-user"></span>
        <input type="text" name="full_name" class="form-control" placeholder="Name" required>
    </div>
    <div class="form-group customised-formgroup"> <span class="icon-envelope"></span>
        <input type="email" name="email" class="form-control" placeholder="Email" required>
    </div>
    <!--<div class="form-group customised-formgroup"> <span class="icon-telephone"></span>
        <input type="text" name="phone" class="form-control" placeholder="Phone (optional)">
    </div>-->
    <div class="form-group customised-formgroup"> <span class="icon-laptop"></span>
        <input type="text" name="website" class="form-control" placeholder="Website (optional)">
    </div>
    <!--<div class="form-group customised-formgroup"> <span class="icon-bubble"></span>
        <textarea name="message" class="form-control" placeholder="Message"></textarea>
    </div>-->
    <div>
        <br>
        <input type="submit" name="submit" value="Submit" />
    </div>
    </div>
        </div>
    </div>
</form>
</body>
</html>