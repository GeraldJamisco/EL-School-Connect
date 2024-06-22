<?php require_once('header.php'); ?>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
foreach ($result as $row) {
    $banner_registration = $row['banner_registration'];
}
?>

<div class="container text-center my-3" style=" display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    height: 40vh;"> 
    <button class="btn btn-danger" style="  color: inherit;
    text-decoration: none;">
        <a href="registration.php" style="padding: 50px;"> Buy </a>
    </button>
    <br><br>
<button class="btn btn-success">
    <a href="admin/registration.php" style="padding: 50px;"> Sell </a>
</button></div>



<?php require_once('footer.php'); ?>