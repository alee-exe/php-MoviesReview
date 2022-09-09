<?php
// Made by Andy Lee
date_default_timezone_set('GMT');
session_start();
$link = mysqli_connect("localhost", "root", "", "movieweb_db");

if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit();
}

$userID = $_SESSION['userID'];

$resultType = mysqli_query($link, "SELECT User_Type FROM User WHERE User_ID='$userID'");
$userType = mysqli_fetch_assoc($resultType)['User_Type'];

// Get current date in format YYYY-MM-DD 00:00:00 (24H format) as a String
$currentDateTime = date('Y-m-d H:i:s');
// Adds 1 month to $currentDateTime 
// $currentDateTime is first converted into Time format
// We add 1 month to $currentDateTime in Time format then convert it back to String format which is the same as above
$expireDateTime = date('Y-m-d H:i:s', strtotime('+1 month', strtotime($currentDateTime)));

if ($userType != 'P') {
    $updateType = mysqli_query($link, "UPDATE User SET User_Type='T' WHERE User_ID='$userID'");
}

$insertTrial = mysqli_query($link, "INSERT INTO Trial(User_ID, Start_Date, Expire_Date) VALUES ('$userID', '$currentDateTime', '$expireDateTime')");

if ($insertTrial) {
    header('refresh:5; moviereview.php');
    echo "<div style='font:20px sans-serif;'>Free Trial successfully started!</div>";
    echo "<div style='font:20px sans-serif;'>Redirecting to main page in 5 seconds...</div>";
} else {
    if ($userType == "P") {
        header('refresh:10; moviereview.php');
        echo "<div style='font:20px sans-serif;'>You cannot have a Free Trial as you are already on a Membership!</div><br></br>";
        echo "<div style='font:20px sans-serif;'>Redirecting to main page in 10 seconds...</div>";
    } else if ($userType = "T") {
        header('refresh:10; moviereview.php');
        echo "<div style='font:20px sans-serif;'>You already have a Free Trial!</div><br></br>";
        echo "<div style='font:20px sans-serif;'>Your Free Trial ends at: $expireDateTime</div><br></br>";
        echo "<div style='font:20px sans-serif;'>Redirecting to main page in 10 seconds...</div>";
    } else {
        echo "<div style='font:20px sans-serif;'>ERROR: Something went wrong...</div>";
        echo mysqli_error($link);
    }
}

mysqli_free_result($resultType);
mysqli_close($link);
?>