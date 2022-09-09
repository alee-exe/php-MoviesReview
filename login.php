<!DOCTYPE HTML>
<!-- Made by Andy Lee-->
<?php
// If the form submit button was pressed, execute this script
if (isset($_POST['loginSubmit'])) {
    date_default_timezone_set('GMT');
    session_start();
    $link = mysqli_connect("localhost", "root", "", "movieweb_db");

    if (!$link) {
        echo "Error: Unable to connect to MySQL." . PHP_EOL;
        echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
        echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
        exit();
    }

    // Escape string variable to prevent SQL injections or uninended behaviour
    $email = mysqli_escape_string($link, $_POST['email']);
    $password = mysqli_escape_string($link, $_POST['password']);

    // Email is used here to login as it is private to each User (whereas Username is public)
    $resultLogin = mysqli_query($link, "SELECT * FROM User WHERE Email='$email' && Password='$password'");
    // Returns the number of rows that match the query $resultLogin
    $checkLogin = mysqli_num_rows($resultLogin);

    // If there is a row in the User table that has the same Email and Password via POST (User is registered)
    if ($checkLogin == 1) {
        // Since a mysqli_query just performs a query against the database, we need to somehow retrieve the data
        // Store this data as session variables which will keep track of User 
        $userRow = mysqli_fetch_assoc($resultLogin);
        $_SESSION['username'] = $userRow['Username'];
        $_SESSION['userID'] = $userRow['User_ID'];
        header('location: moviereview.php');
    } else {
        // Redirect login page to refresh input boxes
        header('refresh:3; login.php');
        echo "<div style='font:20px sans-serif;'>&nbspInvalid Email or Password.</div>";
        echo "<div style='font:20px sans-serif;'>&nbspThis page will redirect in 3 seconds...</div>";
    }

    // Frees memory and closes database connection
    mysqli_free_result($resultLogin);
    mysqli_close($link);
}
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" href="movie.ico" type="image">
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <title>Login Page</title>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="span6" style="float:none; margin:auto;">
                <h2>Login Account</h2>
                <form action="login.php" method="post">
                    <div class="form-group">
                        Email:
                        <input type="text" name="email" class="form-control" required oninvalid="this.setCustomValidity('Email is required.')" oninput="setCustomValidity('')">
                    </div>
                    <div class="form-group">
                        Password:
                        <input type="password" name="password" class="form-control" required oninvalid="this.setCustomValidity('Password is required.')" oninput="setCustomValidity('')">
                    </div>
                    <button type="submit" class="btn btn-primary" name="loginSubmit">Login</button><br></br>
                    <p>Don't have an account? Click <a href="register.php">here</a> to sign-up!<p>
                </form>
            </div>
        </div>
</body>

</html>