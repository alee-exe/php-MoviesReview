<!DOCTYPE HTML>
<!-- Made by Andy Lee-->
<?php
if (isset($_POST['editWatchlistSubmit'])) {
    date_default_timezone_set('GMT');
    session_start();
    $link = mysqli_connect("localhost", "root", "", "movieweb_db");

    if (!$link) {
        echo "Error: Unable to connect to MySQL." . PHP_EOL;
        echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
        echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
        exit();
    }

    $name = $_POST['name'];
    $desc = $_POST['description'];
    $username = $_SESSION['username'];

    // If nothing is submitted in the Watchlist name field
    if ($name == null) {
        // If a Watchlist name session variable is already set, use it as the current name
        if (isset($_SESSION['watchlistName'])) {
            $name = $_SESSION['watchlistName'];
        }
        // If something is submited in the Watchlist name field, use it as the current description
    } else {
        $_SESSION['watchlistName'] = $name;
    }

    // If nothing is submitted in the Watchlist description field
    if ($desc == null) {
        // If a Watchlist description session variable is already set, use it as the current description
        if (isset($_SESSION['watchlistDesc'])) {
            $desc = $_SESSION['watchlistDesc'];
        }
    } else {
        // If something is submited in the Watchlist description field, set it to the session variable
        $_SESSION['watchlistDesc'] = $desc;
    }

    $updateWatchlist = mysqli_query($link, "UPDATE User SET List_Name=\"$name\", List_Description=\"$desc\" WHERE Username=\"$username\"");

    if ($updateWatchlist) {
        header('refresh:3; moviereview.php');
        echo "<div style='font:20px sans-serif;'>&nbspWatchlist updated sucessfully!</div>";
        echo "<div style='font:20px sans-serif;'>&nbspRedirecting to main page in 3 seconds...</div>";
    } else {
        header('refresh:10; editwatchlist.php');
        echo "<div style='font:20px sans-serif;'>&nbspERROR: Could not update Watchlist...</div>";
        echo "<div style='font:20px sans-serif;'>&nbspThis page will redirect in 10 seconds...</div>";
        echo mysqli_error($link);
    }
    mysqli_close($link);
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="movie.ico" type="image">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <title>Edit Watchlist</title>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="span6" style="float:none; margin:auto;">
                <h2>Edit Watchlist</h2>
                <form action="watchlistedit.php" method="post">
                    <div class="form-group">
                        Name:
                        <input type="text" name="name" class="form-control">
                    </div>
                    <div class="form-group">
                        Description:
                        <input type="text" name="description" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary" name="editWatchlistSubmit">Edit</button><br></br>
                    <p><b>Note:</b> If a field is left blank, the previous values are used.</p>
                </form>
            </div>
        </div>
</body>
</html>