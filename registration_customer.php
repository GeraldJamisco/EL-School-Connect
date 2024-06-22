<?php
include_once 'header.php';
if (isset($_POST['form1'])) {

$valid = 1;

if(empty($_POST['cust_name'])) {
    $valid = 0;
    $error_message .= LANG_VALUE_123."<br>";
}

if(empty($_POST['cust_email'])) {
    $valid = 0;
    $error_message .= LANG_VALUE_131."<br>";
} else {
    if (filter_var($_POST['cust_email'], FILTER_VALIDATE_EMAIL) === false) {
        $valid = 0;
        $error_message .= LANG_VALUE_134."<br>";
    } else {
        $statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_email=?");
        $statement->execute(array($_POST['cust_email']));
        $total = $statement->rowCount();                            
        if($total) {
            $valid = 0;
            $error_message .= LANG_VALUE_147."<br>";
        }
    }
}

if(empty($_POST['cust_phone'])) {
    $valid = 0;
    $error_message .= LANG_VALUE_124."<br>";
}


if(empty($_POST['cust_country'])) {
    $valid = 0;
    $error_message .= LANG_VALUE_126."<br>";
}

if( empty($_POST['cust_password']) || empty($_POST['cust_re_password']) ) {
    $valid = 0;
    $error_message .= LANG_VALUE_138."<br>";
}

if( !empty($_POST['cust_password']) && !empty($_POST['cust_re_password']) ) {
    if($_POST['cust_password'] != $_POST['cust_re_password']) {
        $valid = 0;
        $error_message .= LANG_VALUE_139."<br>";
    }
}
// if(isset($_POST['form1'])) {
//     echo '<pre>';
//     print_r($_POST);
//     echo '</pre>';
//     // ... rest of the code
// }


if($valid == 1) {

    $token = md5(time());
    $cust_datetime = date('Y-m-d h:i:s');
    $cust_timestamp = time();
    // echo $token;
    // saving into the database
    $statement = $pdo->prepare("INSERT INTO tbl_customer (
                                    cust_name,
                                    cust_cname,
                                    cust_email,
                                    cust_phone,
                                    cust_country,
                                    cust_b_name,
                                    cust_b_cname,
                                    cust_b_phone,
                                    cust_b_country,
                                    cust_b_address,
                                    cust_b_city,
                                    cust_b_state,
                                    cust_b_zip,
                                    cust_s_name,
                                    cust_s_cname,
                                    cust_s_phone,
                                    cust_s_country,
                                    cust_s_address,
                                    cust_s_city,
                                    cust_s_state,
                                    cust_s_zip,
                                    cust_password,
                                    cust_token,
                                    cust_datetime,
                                    cust_timestamp,
                                    cust_status
                                ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $statement->execute(array(
                                    strip_tags($_POST['cust_name']),
                                    strip_tags($_POST['cust_cname']),
                                    strip_tags($_POST['cust_email']),
                                    strip_tags($_POST['cust_phone']),
                                    strip_tags($_POST['cust_country']),
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    md5($_POST['cust_password']),
                                    $token,
                                    $cust_datetime,
                                    $cust_timestamp,
                                    0
                                ));

    // Send email for confirmation of the account
    $to = $_POST['cust_email'];
    
    $subject = LANG_VALUE_150;
    $verify_link = BASE_URL.'verify.php?email='.$to.'&token='.$token;
    $message = ''.LANG_VALUE_151.'<br><br>

<a href="'.$verify_link.'">'.$verify_link.'</a>';

    $headers = "From: noreply@" . BASE_URL . "\r\n" .
               "Reply-To: noreply@" . BASE_URL . "\r\n" .
               "X-Mailer: PHP/" . phpversion() . "\r\n" . 
               "MIME-Version: 1.0\r\n" . 
               "Content-Type: text/html; charset=ISO-8859-1\r\n";
    
    // Sending Email
    mail($to, $subject, $message, $headers);

    unset($_POST['cust_name']);
    unset($_POST['cust_cname']);
    unset($_POST['cust_email']);
    unset($_POST['cust_phone']);
    unset($_POST['cust_address']);


    $success_message = LANG_VALUE_152;
}
}

?>