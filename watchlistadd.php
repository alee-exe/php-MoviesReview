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

$movieID = $_SESSION['movieID'];

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $resultUserID = mysqli_query($link, "SELECT User_ID FROM User WHERE Username='$username'");
    $userID = mysqli_fetch_row($resultUserID)[0];

    // Checks if Movie is already added into watchlist
    $resultCheckWatchlist = mysqli_query($link, "SELECT * FROM Watchlist_adds WHERE User_ID='$userID' && Movie_ID='$movieID'");

    if (mysqli_num_rows($resultCheckWatchlist) == 1) {
        header('refresh:5; moviepage.php?movieid=' . $movieID);
        echo "<div style='font:20px sans-serif;'>This Movie is already in your watchlist!</div><br></br>";
        echo "<div style='font:20px sans-serif;'>Redirecting to movie page in 5 seconds...!</div><br></br>";
    } else {
        $insertIntoWatchlist = mysqli_query($link, "INSERT INTO Watchlist_adds (User_ID, Movie_ID) VALUES ('$userID', '$movieID')");
        if($insertIntoWatchlist) {
            header('refresh:5; moviepage.php?movieid=' . $movieID);
            echo "<div style='font:20px sans-serif;'>Successfully added to your watchlist!</div><br></br>";
            echo "<div style='font:20px sans-serif;'>Redirecting to movie page in 5 seconds...!</div><br></br>";
            mysqli_free_result($resultCheckWatchlist);
        } else {
            echo "<div style='font:20px sans-serif;'>ERROR: Could not add to watchlist...</div><br></br>";
            echo mysqli_error($link);
        }

    }
    mysqli_free_result($resultUserID);
} else {
    header('refresh:5; moviepage.php?movieid=' . $movieID);
    echo "<div style='font:20px sans-serif;'>You must login to add to a watchlist!</div><br></br>";
    echo "<div style='font:20px sans-serif;'>Redirecting to movie page in 5 seconds...!</div><br></br>";
}

mysqli_close($link);
?>