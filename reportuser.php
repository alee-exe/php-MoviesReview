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

if (isset($_POST['reporteeID'])) {
    $_SESSION['reporteeID'] = (int) $_POST['reporteeID'];
} 

if (isset($_SESSION['userID'])) {
    if (isset($_POST['report'])) {
        $category = mysqli_escape_string($link, $_POST['reportCategory']);
        $comment = mysqli_escape_string($link, $_POST['reportComment']);
        $reporteeID = $_SESSION['reporteeID'];
        $reporterID = $_SESSION['userID'];;
        $currentDateTime = date('Y-m-d H:i:s');
        $resultReporteeUsername = mysqli_query($link, "SELECT Username FROM User WHERE User_ID='$reporteeID'");
        $reporteeUsername = mysqli_fetch_assoc($resultReporteeUsername)['Username'];
        $insertReport = mysqli_query($link, "INSERT INTO User_reports (Reporter_ID, Reportee_ID, Category, Comment, Report_Date) VALUES ('$reporterID', '$reporteeID', '$category', '$comment', '$currentDateTime')");

        if ($insertReport) {
            header('refresh:5;' . $_SESSION['moviePage']);
            echo "<div style='font:20px sans-serif;'>&nbspSucessfully reported User $reporteeUsername.</div>";
            echo "<div style='font:20px sans-serif;'>&nbspThis page will redirect to movie page in 5 seconds...</div>";
        } else {
            echo "ERROR: Could not insert User report...";
            echo mysqli_error($link);
        }
    }
}

mysqli_close($link);
?>