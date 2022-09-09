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

// Checks if a User has submitted a review for this movie
if (isset($_POST['submitReview'])) {
    if (isset($_SESSION['username'])) {
        $userID = $_SESSION['userID'];
        $movieID = $_SESSION['movieID'];
        $checkSubmittedReview = mysqli_query($link, "SELECT * FROM User_reviews WHERE User_ID='$userID' && Movie_ID='$movieID'");
        if (mysqli_num_rows($checkSubmittedReview) == 1) {
            header('refresh:10; moviepage.php?movieid=' . $movieID);
            echo "<div style='font:20px sans-serif;'>You've already submitted a review!</div><br></br>";
            echo "<div style='font:20px sans-serif;'>You can only submit one review per movie!</div><br></br>";
            echo "<div style='font:20px sans-serif;'>Redirecting to movie page in 10 seconds...!</div><br></br>";
        } else {
            $rating = mysqli_escape_string($link, $_POST['rating']);
            $comment = mysqli_escape_string($link, $_POST['review']);
            $insertReview = mysqli_query($link, "INSERT INTO User_reviews (User_ID, Movie_ID, Rating, Comment) VALUES ('$userID', '$movieID', '$rating', '$comment')");
            header('location: moviepage.php?movieid=' . $movieID);
        }
    } else {
        header('refresh:5; moviepage.php?movieid=' . $movieID);
        echo "<div style='font:20px sans-serif;'>You must login to comment!</div><br></br>";
        echo "<div style='font:20px sans-serif;'>Redirecting to movie page in 5 seconds...!</div><br></br>";
    }
} else {
    echo "<div style='font:20px sans-serif;'>ERROR: Someting went wrong!</div><br></br>";
    echo mysqli_error($link);

}
mysqli_close($link);
?>