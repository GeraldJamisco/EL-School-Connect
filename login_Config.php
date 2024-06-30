<?php
session_start();
include_once 'header.php';

if(isset($_POST['form1'])) {
        
    if(empty($_POST['cust_email']) || empty($_POST['cust_password'])) {
        $error_message = LANG_VALUE_132;
    } else {
        
        $cust_email = strip_tags($_POST['cust_email']);
        $cust_password = strip_tags($_POST['cust_password']);

        $statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_email=?");
        $statement->execute(array($cust_email));
        $total = $statement->rowCount();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach($result as $row) {
            $cust_status = $row['cust_status'];
            $row_password = $row['cust_password'];
        }

        if($total==0) {
            $error_message .= LANG_VALUE_133;
        } else {
            //using MD5 form
            if( $row_password != md5($cust_password) ) {
                $error_message .= LANG_VALUE_139;
            } else {
                if($cust_status == 0) {
                    $error_message .= LANG_VALUE_148;
                } else {
                    $_SESSION['customer'] = $row;
                    header("Location: dashboard.php");
                }
            }
            
        }
    }
}


// if ($error_message != '') {
//     echo "<script type='text/javascript'>
//             alert('".$error_message."');
//             window.location.href = 'login.php';
//           </script>";
//     exit;
// }else {
//     # code for making user go straight to the dashboard
//    header("Location: dashboard.php");   
// }

// if($success_message1 != '') {
//     echo "<script>alert('".$success_message1."')</script>";
//     header('location: product.php?id='.$_REQUEST['id']);
// }


?>