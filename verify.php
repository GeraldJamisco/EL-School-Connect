<?php require_once('header.php'); ?>

<?php
$error_message = '';
$success_message = '';

// Ensure both email and token are present
if (isset($_REQUEST['email']) && isset($_REQUEST['token'])) {

    // check if the token is correct and matches with database.
    $statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_email=?");
    $statement->execute(array($_REQUEST['email']));
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    
    $valid = false;
    foreach ($result as $row) {
        if ($_REQUEST['token'] == $row['cust_token']) {
            $valid = true;
        }
    }

    if ($valid) {
        // everything is correct. now activate the user by removing token value from database.
        $statement = $pdo->prepare("UPDATE tbl_customer SET cust_token='', cust_status=1 WHERE cust_email=?");
        $statement->execute(array($_REQUEST['email']));

        $success_message = '<p style="color:green;">Your email is verified successfully. You can now login to your account and continue shopping.</p>
                            <p><a href="login.php" style="color:#167ac6;font-weight:bold;">Click here to login</a></p>';
    } else {
        header('location: index.php');
        exit;
    }
} else {
    // Redirect if email or token is not set
    header('location: index.php');
    exit;
}
?>

<div class="page-banner" style="background-color:#444;">
    <div class="inner">
        <h1>Registration Successful</h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="user-content">
                    <?php 
                        echo $error_message;
                        echo $success_message;
                    ?>
                </div>                
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>
