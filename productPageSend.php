<?php
include 'header.php';
// session_start(); // Ensure sessions are started

if (isset($_POST['form_add_to_cart'])) {

    // Validate and sanitize inputs
    $productId = filter_var($_REQUEST['id'], FILTER_SANITIZE_NUMBER_INT);
    $quantity = filter_var($_POST['p_qty'], FILTER_SANITIZE_NUMBER_INT);
    $currentPrice = filter_var($_POST['p_current_price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $productName = filter_var($_POST['p_name']);
    $featuredPhoto = filter_var($_POST['p_featured_photo']);

    // Ensure the product exists and get current stock
    $statement = $pdo->prepare("SELECT p_qty FROM tbl_product WHERE p_id=?");
    $statement->execute([$productId]);
    $product = $statement->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo '<script>alert("Product not found.");</script>';
        header('Location: product.php?id=' . $_REQUEST['id']);
        // exit;
    }

    $current_p_qty = $product['p_qty'];

    if ($quantity > $current_p_qty) {
        $temp_msg = 'Sorry! There are only '.$current_p_qty.' item(s) in stock';
        echo '<script>alert("'.$temp_msg.'");</script>';
        header('Location: product.php?id=' . $_REQUEST['id']);

    } else {
        // Initialize cart session arrays if not already set
        if (!isset($_SESSION['cart_p_id'])) {
            $_SESSION['cart_p_id'] = [];
            $_SESSION['cart_size_id'] = [];
            $_SESSION['cart_size_name'] = [];
            $_SESSION['cart_color_id'] = [];
            $_SESSION['cart_color_name'] = [];
            $_SESSION['cart_p_qty'] = [];
            $_SESSION['cart_p_current_price'] = [];
            $_SESSION['cart_p_name'] = [];
            $_SESSION['cart_p_featured_photo'] = [];
        }

        $size_id = isset($_POST['size_id']) ? filter_var($_POST['size_id'], FILTER_SANITIZE_NUMBER_INT) : 0;
        $color_id = isset($_POST['color_id']) ? filter_var($_POST['color_id'], FILTER_SANITIZE_NUMBER_INT) : 0;

        // Fetch size and color names if applicable
        $size_name = '';
        if ($size_id) {
            $statement = $pdo->prepare("SELECT size_name FROM tbl_size WHERE size_id=?");
            $statement->execute([$size_id]);
            $size = $statement->fetch(PDO::FETCH_ASSOC);
            $size_name = $size ? $size['size_name'] : '';
        }

        $color_name = '';
        if ($color_id) {
            $statement = $pdo->prepare("SELECT color_name FROM tbl_color WHERE color_id=?");
            $statement->execute([$color_id]);
            $color = $statement->fetch(PDO::FETCH_ASSOC);
            $color_name = $color ? $color['color_name'] : '';
        }

        // Check if product is already in the cart
        $added = false;
        foreach ($_SESSION['cart_p_id'] as $key => $value) {
            if ($_SESSION['cart_p_id'][$key] == $productId && 
                $_SESSION['cart_size_id'][$key] == $size_id && 
                $_SESSION['cart_color_id'][$key] == $color_id) {
                $added = true;
                break;
            }
        }

        if ($added) {
            $error_message1 = 'This product is already added to the shopping cart.';
            echo '<script>alert("'.$error_message1.'");</script>';
            header('Location: product.php?id=' . $_REQUEST['id']);
        } else {
            $new_key = count($_SESSION['cart_p_id']) + 1;

            // Add product to the cart session arrays
            $_SESSION['cart_p_id'][$new_key] = $productId;
            $_SESSION['cart_size_id'][$new_key] = $size_id;
            $_SESSION['cart_size_name'][$new_key] = $size_name;
            $_SESSION['cart_color_id'][$new_key] = $color_id;
            $_SESSION['cart_color_name'][$new_key] = $color_name;
            $_SESSION['cart_p_qty'][$new_key] = $quantity;
            $_SESSION['cart_p_current_price'][$new_key] = $currentPrice;
            $_SESSION['cart_p_name'][$new_key] = $productName;
            $_SESSION['cart_p_featured_photo'][$new_key] = $featuredPhoto;

            $success_message1 = 'Product is added to the cart successfully!';
            echo '<script>alert("'.$success_message1.'");</script>';
        }
    }
}

if($error_message1 != '') {
    echo "<script>alert('".$error_message1."')</script>";
}
if($success_message1 != '') {
    echo "<script>alert('".$success_message1."')</script>";
    header('location: product.php?id='.$_REQUEST['id']);
}
?>
