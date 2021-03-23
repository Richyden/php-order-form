<?php
//this line makes PHP behave in a more strict way
declare(strict_types=1);

//we are going to use session variables so we need to enable sessions
session_start();

function whatIsHappening() {
    echo '<h2>$_GET</h2>';
    var_dump($_GET);
    echo '<h2>$_POST</h2>';
    var_dump($_POST);
    echo '<h2>$_COOKIE</h2>';
    var_dump($_COOKIE);
    echo '<h2>$_SESSION</h2>';
    var_dump($_SESSION);
}

//your products with their price.
$pizza = [
    ['name' => 'Margherita', 'price' => 8],
    ['name' => 'Hawaï', 'price' => 8.5],
    ['name' => 'Salami pepper', 'price' => 10],
    ['name' => 'Prosciutto', 'price' => 9],
    ['name' => 'Parmiggiana', 'price' => 9],
    ['name' => 'Vegetarian', 'price' => 8.5],
    ['name' => 'Four cheeses', 'price' => 10],
    ['name' => 'Four seasons', 'price' => 10.5],
    ['name' => 'Scampi', 'price' => 11.5]
];

$soft = [
    ['name' => 'Water', 'price' => 1.8],
    ['name' => 'Sparkling water', 'price' => 1.8],
    ['name' => 'Cola', 'price' => 2],
    ['name' => 'Fanta', 'price' => 2],
    ['name' => 'Sprite', 'price' => 2],
    ['name' => 'Ice-tea', 'price' => 2.2],
];

$totalValue = 0;

//=== Check inputs and Validity ===
$mail_Error = $street_Error = $street_Num_Error = $street_int_Error = $city_Error = $mail_validity = $zipcode_Error = $zipcode_Int_Error = "";

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    //===Check Mail ===
    if(empty($_POST['email'])) {
        $mail_Error = "Enter a email please.";
    } else {
        if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $mail = $_POST['email'];
        }else {
            $mail_validity = $_POST['email'] . " is not valid. Please enter a valid email.";
        }
    }

    //=== Check Street ===
    if(empty($_POST['street'])) {
        $street_Error = "Enter a street please.";
    }else {
        $street = $_POST['street'];
    }

    //=== Check Number Street and number ===
    if(empty($_POST['streetnumber'])) {
        $street_int_Error = "Enter a number please.";
    }else {
        if(filter_var($_POST['streetnumber'], FILTER_VALIDATE_INT)) {
            $street_Number = $_POST['streetnumber'];
        }else {
            $street_int_Error = "Street number must be a number.";
        }
    }

    //=== Check City ===
    if(empty($POST['city'])) {
        $city_Error = "Enter a city name please.";
    }else {
        $city = $_POST['city'];
    }

    //=== Check Zipcode and number ===
    if(empty($_POST['zipcode'])) {
        $zipcode_Error = "Enter a zipcode please.";
    }else {
        if(filter_var($_POST['zipcode'], FILTER_VALIDATE_INT)) {
            $zipcode = $_POST['zipcode'];
        }else {
            $zipcode_Int_Error = "Zipcode must be a number.";
        }
    }
}

$products = $pizza;

// toggle between links
if (isset($_GET['food'])) {
    $value = $_GET['food'];
    if ($value == 'drinks') {
        $products = $soft;
    }
};

// === Get Curent Time ===

$current_Time = date_create("now", new DateTimeZone('Europe/Brussels'))->format('G:i');

//=== If express delivery is chack ===

$common_Delivery = date("G:i", strtotime('+1 hour', strtotime($current_Time)));
$express_Delivery = date("G:i", strtotime('+30 minutes', strtotime($current_Time)));

if(isset($_POST['express_delivery'])) {
    $deliveryTime = $express_Delivery;
    $totalValue += 5;
}else {
    $deliveryTime = $common_Delivery;
}

//=== Calcul Price ===
if(isset($_POST['products'])) {
    $products_Select = $_POST['products'];
    foreach($products_Select AS $i => $choice) {
        $choice = $products[$i]['price'];
        $totalValue += $choice;
    }
    $_SESSION['total-price'] = $totalValue;
}

//
if(isset($mail, $street, $street_Number, $city, $zipcode, $totalValue, $deliveryTime)) {
    
    //=== Session user input ===
    $_SESSION["email"] = $email;
    $_SESSION["street"] = $street;
    $_SESSION["street_Number"] = $street_Number;
    $_SESSION["city"] = $city;
    $_SESSION["zipcode"] = $zipcode;

    if($totalValue == 0) {
        $invalid_Form = "Invalid order.";
    }elseif ($totalValue == 5 && isset($_POST['express_delivery'])) {
        $invalid_Form = "Invalid order.";
    }else {
        $form_Complete = "Your order placed with the email <strong>'$mail'</strong> has been completed. </br>You payed <strong>&euro; $totalValue</strong></br> Your order has been sent to the following address: <strong>$street n° $street_Number, $city $zipcode</strong>.</br>Delivery is expected at: <strong>$deliveryTime</strong>";
    }
}

require 'form-view.php';