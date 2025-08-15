<html>

<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
</head>
<style>
.box {
    width: 100%;
    max-width: 600px;
    background-color: #f9f9f9;
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 16px;
    margin: 0 auto;
}

input.parsley-success,
select.parsley-success,
textarea.parsley-success {
    color: #468847;
    background-color: #DFF0D8;
    border: 1px solid #D6E9C6;
}

input.parsley-error,
select.parsley-error,
textarea.parsley-error {
    color: #B94A48;
    background-color: #F2DEDE;
    border: 1px solid #EED3D7;
}

.parsley-errors-list {
    margin: 2px 0 3px;
    padding: 0;
    list-style-type: none;
    font-size: 0.9em;
    line-height: 0.9em;
    opacity: 0;

    transition: all .3s ease-in;
    -o-transition: all .3s ease-in;
    -moz-transition: all .3s ease-in;
    -webkit-transition: all .3s ease-in;
}

.parsley-errors-list.filled {
    opacity: 1;
}

.parsley-type,
.parsley-required,
.parsley-equalto {
    color: #ff0000;
}

.error {
    color: red;
    font-weight: 700;
}
</style>
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "C:\\xampp\\htdocs\\KARE_WEBSITE2\\PHPMailer-master\\src\\PHPMailer.php";
require "C:\\xampp\\htdocs\\KARE_WEBSITE2\\PHPMailer-master\\src\\SMTP.php";
require "C:\\xampp\\htdocs\\KARE_WEBSITE2\\PHPMailer-master\\src\\Exception.php";

include("connection.php");



$email='';
if(isset($_REQUEST['pwdrst']))
{
  $email = $_REQUEST['email'];
  $check_email = mysqli_query($con,"select email from users where email='$email'");
  $res = mysqli_num_rows($check_email);
  if($res>0)
  {
    $message = '<div>
     <p><b>Hello!</b></p>
     <p>You are recieving this email because we recieved a password reset request for your account.</p>
     <br>
     <p><button class="btn btn-primary"><a href="http://localhost/KARE_WEBSITE2/passwordreset.php?secret='.base64_encode($email).'">Reset Password</a></button></p>
     <br>
     <p>If you did not request a password reset, no further action is required.</p>
    </div>';

    $email = $_REQUEST['email'];
    $mail = new PHPMailer(true);
    $recipient_address = $email;
    
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server address
            $mail->SMTPAuth = true;
            $mail->Username = 'arunkumar97462@gmail.com'; // Replace with your SMTP username
            $mail->Password = ''; // Replace with your SMTP password
            $mail->SMTPSecure = 'ssl'; // Use 'tls' or 'ssl' based on your SMTP server
            $mail->Port = 465; // The SMTP port
    
            $mail->setFrom('arunkumar97462@gmail.com', 'KareSlots'); // Replace with your email and name
            $mail->addAddress($recipient_address); // Email and name of the recipient
    
            $mail->isHTML(true);
            $mail->Subject = 'Reset Password';
$mail->Body =$message;



if($mail->send())
{
  $msg = "We have e-mailed your password reset link!";
}
}
else
{
  $msg = "We can't find a user with that email address";
}
}
?>

<body>
    <div class="container">
        <div class="table-responsive">
            <h3 align="center">Forgot Password</h3><br />
            <div class="box">
                <form id="validate_form" method="post">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="text" name="email" id="email" placeholder="Enter Email" required
                            data-parsley-type="email" data-parsley-trigg er="keyup" class="form-control">
                    </div>
                    <div class="form-group">
                        <input type="submit" id="login" name="pwdrst" value="Send Password Reset Link"
                            class="btn btn-success" />
                    </div>

                    <p class="error"><?php if(!empty($msg)){ echo $msg; } ?></p>
                </form>
            </div>
        </div>
    </div>
</body>

</html>