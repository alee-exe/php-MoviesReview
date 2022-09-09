<!DOCTYPE HTML>
<!-- Made by Andy Lee-->
<?php
if (isset($_POST['submitMovie'])) {
    date_default_timezone_set('GMT');
    session_start();
    $link = mysqli_connect("localhost", "root", "", "movieweb_db");

    if (!$link) {
        echo "Error: Unable to connect to MySQL." . PHP_EOL;
        echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
        echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
        exit();
    }

    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        $title = mysqli_escape_string($link, $_POST['title']);
        $director = mysqli_escape_string($link, $_POST['director']);
        $releaseDate = mysqli_escape_string($link, $_POST['releaseDate']);
        $genre1 = mysqli_escape_string($link, $_POST['genre1']);
        $genre2 = mysqli_escape_string($link, $_POST['genre2']);
        $genre3 = mysqli_escape_string($link, $_POST['genre3']);
        $actors = mysqli_escape_string($link, $_POST['actors']);

        $userID = $_SESSION['userID'];
        // Checks if file (image cover) was successfully submitted to server
        $checkFile = getimagesize($_FILES['cover']['tmp_name']);

        if ($checkFile) {
            // Gets temporary image file uploaded to server (via the server's file image name)
            $imageTemp = $_FILES['cover']['tmp_name'];
            // Gets the full image content uploaded to server using server's file image name as reference
            $imageContent = addslashes(file_get_contents($imageTemp));
            $insertMovie = mysqli_query($link, "INSERT INTO Movie(Title, Release_Date, Cover, Director, User_ID) VALUES ('$title', '$releaseDate', '$imageContent', '$director', '$userID')");
            $movieID = mysqli_insert_id($link);

            // Puts each actor into a separate index in an array
            $actorsExploded = explode(", ", $actors);

            for ($i = 0; $i < sizeof($actorsExploded); $i++) {
                $individualActor = explode(" ", $actorsExploded[$i]);
                $actorForename = $individualActor[0];
                $actorSurname = $individualActor[1];
                // Removes brackets and underscore from Role to be stored into database
                $actorRole = str_replace("_", " ", (str_replace(")", "", str_replace("(", "", $individualActor[2]))));

                $resultCheckActor = mysqli_query($link, "SELECT * FROM Actor WHERE Forename='$actorForename' && Surname='$actorSurname'");
                $checkActor = mysqli_num_rows($resultCheckActor);

                // Checks if Actor already exists in the database
                // However, this might be a problem for Actors with the same name (so we assume each actor has a unique name)
                if ($checkActor > 0) {
                    // If it does, just add actor to Movie_cast table
                    $actorID = mysqli_fetch_assoc($resultCheckActor)['Actor_ID'];
                    $insertRole = mysqli_query($link, "INSERT INTO Movie_Cast (Movie_ID, Actor_ID, Role) VALUES ('$movieID', '$actorID', '$actorRole')");
                } else {
                    // If it doesn't, then add actor to Actor and Movie_cast table
                    $insertActor = mysqli_query($link, "INSERT INTO Actor (Forename, Surname) VALUES ('$actorForename', '$actorSurname')");
                    $actorID = mysqli_insert_id($link);
                    $insertRole = mysqli_query($link, "INSERT INTO Movie_Cast (Movie_ID, Actor_ID, Role) VALUES ('$movieID', '$actorID', '$actorRole')");
                }

                if (!$insertActor) {
                    echo "<div style='font:20px sans-serif;'>&nbspERROR: Could not insert Actor into database!</div><br></br>";
                    echo mysqli_error($link);
                }

                mysqli_free_result($resultCheckActor);
            }

            if ($insertMovie) {
                $insertGenre1 = mysqli_query($link, "INSERT INTO Movie_genres(Movie_ID, Genre) VALUES ('$movieID', '$genre1')");
                if ($genre2 != 'empty' && $genre3 == 'empty') {
                    $insertGenre2 = mysqli_query($link, "INSERT INTO Movie_genres(Movie_ID, Genre) VALUES ('$movieID', '$genre2')");
                } else if ($genre2 == 'empty' && $genre3 != 'empty') {
                    $insertGenre3 = mysqli_query($link, "INSERT INTO Movie_genres(Movie_ID, Genre) VALUES ('$movieID', '$genre3')");
                } else if ($genre2 != 'empty' && $genre3 != 'empty') {
                    $insertGenre2 = mysqli_query($link, "INSERT INTO Movie_genres(Movie_ID, Genre) VALUES ('$movieID', '$genre2')");
                    $insertGenre3 = mysqli_query($link, "INSERT INTO Movie_genres(Movie_ID, Genre) VALUES ('$movieID', '$genre3')");
                }
            } else {
                echo "<div style='font:20px sans-serif;'>&nbspERROR: Could not insert Movie into database...</div>";
                echo mysqli_error($link);
            }
        } else {
            echo "<div style='font:20px sans-serif;'>&nbspERROR: Could not retrieve image file uploaded to server..</div>";
            echo mysqli_error($link);
        }
        header('refresh:5; moviereview.php');
        echo "<div style='font:20px sans-serif;'>&nbspMovie succesfully submitted!</div><br></br>";
        echo "<div style='font:20px sans-serif;'>&nbspRedirecting to main page in 5 seconds...</div><br></br>";
    } else {
        header('refresh:5; moviereview.php');
        echo "<div style='font:20px sans-serif;'>&nbspYou must be logged in first to submit a Movie!</div>";
        echo "<div style='font:20px sans-serif;'>&nbspRedirecting to main page in 5 seconds...</div><br></br>";
    }

    mysqli_close($link);
}
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" href="movie.ico" type="image">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha256-pasqAKBDmFT4eHoN2ndd6lN370kFiGUFyTiUHWhU7k8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <title>Submit Movie</title>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="span6" style="float:none; margin:auto;">
                <h2>Submit a Movie</h2>
                <form enctype="multipart/form-data" action="moviesubmit.php" method="post">
                    <div class="form-group">
                        Title:
                        <input type="text" name="title" class="form-control" required oninvalid="this.setCustomValidity('Title is required.')" oninput="setCustomValidity('')">
                    </div>
                    <div class="form-group">
                        Director:
                        <input type="text" name="director" class="form-control" required oninvalid="this.setCustomValidity('Director is required.')" oninput="setCustomValidity('')">
                    </div>
                    <div class="form-group">
                        Release Date:
                        <input type="date" name="releaseDate" class="form-control" required oninvalid="this.setCustomValidity('Release Date is required.')" oninput="setCustomValidity('')">
                    </div>
                    <div class="form-group">
                        Cover:
                        <input type="file" name="cover" accept="image/png,image/jpeg" class="form-control" required oninvalid="this.setCustomValidity('Cover is required.')" oninput="setCustomValidity('')">
                    </div>
                    <div class="form-group">
                        Genre (Required):&nbsp;
                        <select name="genre1">
                            <option value="animation">Animation</option>
                            <option value="action">Action</option>
                            <option value="adventure">Adventure</option>
                            <option value="comedy">Comedy</option>
                            <option value="crime">Crime</option>
                            <option value="drama">Drama</option>
                            <option value="fantasy">Fantasy</option>
                            <option value="historical">Historical</option>
                            <option value="horror">Horror</option>
                            <option value="science fiction">Science Fiction</option>
                            <option value="mystery">Mystery</option>
                            <option value="romance">Romance</option>
                            <option value="social">Social</option>
                        </select>
                    </div>
                    <div class="form-group">
                        Genre (Optional):&nbsp;
                        <select name="genre2">
                            <option value="empty">-</option>
                            <option value="animation">Animation</option>
                            <option value="action">Action</option>
                            <option value="adventure">Adventure</option>
                            <option value="comedy">Comedy</option>
                            <option value="crime">Crime</option>
                            <option value="drama">Drama</option>
                            <option value="fantasy">Fantasy</option>
                            <option value="historical">Historical</option>
                            <option value="horror">Horror</option>
                            <option value="science fiction">Science Fiction</option>
                            <option value="mystery">Mystery</option>
                            <option value="romance">Romance</option>
                            <option value="social">Social</option>
                        </select>
                    </div>
                    <div class="form-group">
                        Genre (Optional):&nbsp;
                        <select name="genre3">
                            <option value="empty">-</option>
                            <option value="animation">Animation</option>
                            <option value="action">Action</option>
                            <option value="adventure">Adventure</option>
                            <option value="comedy">Comedy</option>
                            <option value="crime">Crime</option>
                            <option value="drama">Drama</option>
                            <option value="fantasy">Fantasy</option>
                            <option value="historical">Historical</option>
                            <option value="horror">Horror</option>
                            <option value="science fiction">Science Fiction</option>
                            <option value="mystery">Mystery</option>
                            <option value="romance">Romance</option>
                            <option value="social">Social</option>
                        </select>
                    </div>
                    Actors (Full name and Role required):
                    <textarea name="actors" class="form-control mt-2" placeholder="e.g. Michael Cera (Scott_Pilgrim), Mary Winstead (Romona_Flowers), ... etc." required oninvalid="this.setCustomValidity('An Actor is required.')" oninput="setCustomValidity('')"></textarea>
                    <button type="submit" class="btn btn-primary mt-4" name="submitMovie">Submit</button><br></br>
                    <p><b>Note#1:</b> The maximum file size is 4GB for the Cover.<p>
                            <p><b>Note#2:</b> The Text Box for Actors does not check syntax.<p>
                </form>
            </div>
        </div>
    </div>
</body>

</html>