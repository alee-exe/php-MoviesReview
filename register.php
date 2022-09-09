<!DOCTYPE HTML>
<!-- Made by Andy Lee-->
<?php
if (isset($_POST['registerSubmit'])) {
        date_default_timezone_set('GMT');
        session_start();
        $link = mysqli_connect("localhost", "root", "", "movieweb_db");

        if (!$link) {
            echo "Error: Unable to connect to MySQL." . PHP_EOL;
            echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
            echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
            exit();
        }

        $username = mysqli_escape_string($link, $_POST['username']);
        $email = mysqli_escape_string($link, $_POST['email']);
        $password = $email = mysqli_escape_string($link, $_POST['password']);

        $resultUsername = mysqli_query($link, "SELECT * FROM User WHERE Username='$username'");
        $resultEmail = mysqli_query($link, "SELECT * FROM User WHERE Email='$email'");

        $checkUsername = mysqli_num_rows($resultUsername);
        $checkEmail = mysqli_num_rows($resultEmail);

        // Check if Username and Email are unique
        if ($checkUsername == 1) {
            header('refresh:3; register.php');
            echo "<div style='font:20px sans-serif;'>&nbspUsername is already taken!</div>";
            echo "<div style='font:20px sans-serif;'>&nbspThis page will redirect in 3 seconds...</div>";
        } else if ($checkEmail == 1) {
            header('refresh:3; register.php');
            echo "<div style='font:20px sans-serif;'>&nbspEmail is already taken!</div>";
            echo "<div style='font:20px sans-serif;'>&nbspThis page will redirect in 3 seconds...</div>";
        } else if ($checkUsername == 1 && $checkEmail == 1) {
            header('refresh:3; register.php');
            echo "<div style='font:20px sans-serif;'>&nbspUsername and Email is already taken!</div>";
            echo "<div style='font:20px sans-serif;'>&nbspThis page will redirect in 3 seconds...</div>";
        } else {
            // Otherwise insert new user form input into database 
            mysqli_query($link, "INSERT INTO User(Username, Email, Password) VALUES ('$username', '$email', '$password')");
            echo "<div style='font:20px sans-serif;'>&nbspRegistration successful!</div>";
            echo "<div style='font:20px sans-serif;'>&nbspClick <a href='login.php'>here</a> to login.</div>";
        }

        // Frees memory and closes database connection
        mysqli_free_result($resultUsername);
        mysqli_free_result($resultEmail);
        mysqli_close($link);
    }
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="movie.ico" type="image">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <title>Register Page</title>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="span6" style="float:none; margin:auto;">
                <h2>Register Account</h2>
                <form action="register.php" method="post">
                    <div class="form-group">
                        Username:
                        <input type="text" name="username" class="form-control" required oninvalid="this.setCustomValidity('Username is required.')" oninput="setCustomValidity('')">
                    </div>
                    <div class="form-group">
                        Email:
                        <input type="text" name="email" class="form-control" required oninvalid="this.setCustomValidity('Email is required.')" oninput="setCustomValidity('')">
                    </div>
                    <div class="form-group">
                        Password:
                        <input type="password" name="password" class="form-control" required oninvalid="this.setCustomValidity('Password is required.')" oninput="setCustomValidity('')">
                    </div>
                    <button type="submit" class="btn btn-primary" name="registerSubmit">Register</button><br></br>
                    <p><b>Note:</b> You will be using your email to log into your account.<p>
                </form>
            </div>
        </div>
</body>
</html>