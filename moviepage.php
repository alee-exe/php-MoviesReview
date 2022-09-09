<!DOCTYPE HTML>
<html lang="en">
<!-- Made by Andy Lee-->
<?php
date_default_timezone_set('GMT');
session_start();
$link = mysqli_connect("localhost", "root", "", "movieweb_db");

if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit();
}

if (isset($_SESSION['userID'])) {
    $userID = $_SESSION['userID'];
    $resultType = mysqli_query($link, "SELECT User_Type FROM User WHERE User_ID='$userID'");
    $userType = mysqli_fetch_assoc($resultType)['User_Type'];
    mysqli_free_result($resultType);
}

if (isset($_GET['movieid'])) {
    $movieID = $_GET['movieid'];
    $_SESSION['movieID'] = $movieID;
    $moviePageURL = "moviepage.php?movieid=$movieID";
    $_SESSION['moviePage'] = $moviePageURL;

    $resultMovie = mysqli_query($link, "SELECT Title, Release_Date, Cover, Director, User_ID FROM Movie WHERE Movie_ID='$movieID'");
    $movieRow = mysqli_fetch_assoc($resultMovie);
    $title = $movieRow['Title'];
    $releaseDate = $movieRow['Release_Date'];
    $director = $movieRow['Director'];
    $userIDPosted = $movieRow['User_ID'];

    $resultPostedUsername = mysqli_query($link, "SELECT Username FROM User WHERE User_ID='$userIDPosted'");
    $postedUsername = mysqli_fetch_assoc($resultPostedUsername)['Username'];

    $resultGenres = mysqli_query($link, "SELECT Genre FROM Movie_genres WHERE Movie_ID='$movieID'");
    $resultActors = mysqli_query($link, "SELECT Actor.Forename, Actor.Surname, Movie_cast.Role FROM Actor INNER JOIN Movie_cast ON Actor.Actor_ID = Movie_cast.Actor_ID WHERE Movie_cast.Movie_ID = '$movieID'");
    $resultRating = mysqli_query($link, "SELECT Rating FROM User_reviews WHERE Movie_ID='$movieID'");


    if ($resultGenres) {
        $genres = "";
        // Checks if a row exists in Movie_genres
        // If it does, get the rest of the genres
        while ($rowGenre = mysqli_fetch_assoc($resultGenres)) {
            $genres .= $rowGenre['Genre'] . ", ";
        }

        if ($genres == "") {
            $genres = "No Genres available  ";
        }
        // Removes the last two characters at the end of the String (i.e. comma and space)
        $genres = substr($genres, 0, -2);
        mysqli_free_result($resultGenres);
    }

    if ($resultActors) {
        $actors = "";
        while ($rowActor = mysqli_fetch_assoc($resultActors)) {
            $actors .= $rowActor['Forename'] . " " . $rowActor['Surname'] . " (" . $rowActor['Role'] . "), ";
        }

        if ($actors == "") {
            $actors = "No Actors available  ";
        }
        $actors = substr($actors, 0, -2);
        mysqli_free_result($resultActors);
    }

    if ($resultRating) {
        $totalRating = 0;
        $userCounter = 0;

        while ($rowRating = mysqli_fetch_assoc($resultRating)) {
            $totalRating += $rowRating['Rating'];
            $userCounter++;
        }

        // Avoid artimetic exception (cannot divide by 0)
        if ($userCounter > 0) {
            $avgRating = round($totalRating / $userCounter, 2);
        }

        if ($userCounter == 0) {
            $avgRating = "No Ratings available";
        }
        mysqli_free_result($resultRating);
    }

    if (isset($_SESSION['userID'])) {
        if (isset($_POST['deleteReview'])) {
            $deleteUserComment = mysqli_query($link, "DELETE FROM User_reviews WHERE User_ID='$userID' && Movie_ID='$movieID'");
        }

        if (isset($_POST['editReview'])) {
            $rating = mysqli_escape_string($link, $_POST['editRating']);
            $comment = mysqli_escape_string($link, $_POST['editComment']);
            $editUserComment = mysqli_query($link, "UPDATE User_reviews SET Comment = '$comment', Rating = '$rating' WHERE User_ID='$userID' && Movie_ID='$movieID'");
        }
    }

    mysqli_free_result($resultPostedUsername);
    mysqli_free_result($resultMovie);
}

?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="movie.ico" type="image">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            // Function for adding to watchlist button
            $('.watchlistButton').click(function() {
                window.location = 'watchlistadd.php';
            });

            // Function for getting reporteeID (each report button is assigned a unique userID)
            // If a report button is pressed, the appropriate reporteeID is fetched
            $('.userReport').click(function() {
                var reporteeUserID = this.id;
                $.ajax({
                    type: 'POST',
                    url: 'reportuser.php',
                    data: {
                        'reporteeID': reporteeUserID,
                    },
                    success: function(Response) {
                        console.log("Got reporteeID!");
                    },
                    error: function() {
                        console.log("Could not get reporteeID!");
                    }
                });
            });
        });
    </script>

    <?php
    echo "<title>Movie Review: $title</title>";
    ?>
</head>

<body>
    <nav class="navbar navbar-dark bg-dark">
        <a href="moviereview.php" class="navbar-brand">
            <h2>Movie Review Website</h2>
        </a>
        <?php
        if (!isset($_SESSION['username'])) {
            echo "<button class='navbar-toggler' type='button' onclick=\"window.location.href='login.php';\">Login</button>";
            echo "<button class='navbar-toggler' type='button' onclick=\"window.location.href='register.php';\">Sign-Up</button>";
            echo "<span class='navbar-text'><h2>Login to get Started!</h2></span>";
        } else {
            echo "<button class='navbar-toggler' type='button' onclick=\"window.location.href='logout.php';\">Logout</button>";
            if ($userType == "P" || $userType == "T") {
                echo "<span class='navbar-text text-warning'><h2> Welcome, ", $_SESSION['username'], "</h2></span>";
            } else {
                echo "<span class='navbar-text'><h2> Welcome, ", $_SESSION['username'], "</h2></span>";
            }
        }
        ?>
    </nav>

    <!-- Delete Review Modal -->
    <div class="modal fade" id="deleteReviewModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete this comment?</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this comment?</p>
                    <p class="text-secondary"><small>Once deleted, this comment will be removed from this movie page.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form action=<?php echo $moviePageURL ?> method="post" id="deleteComment">
                        <button type="submit" class="btn btn-primary" name="deleteReview">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Review Modal -->
    <div class="modal fade" id="editReviewModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Review</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action=<?php echo $moviePageURL ?> method="post" id="editComment">
                    <div class="modal-body">
                        <p>Edit your Review:</p>
                        Rating:&nbsp;
                        <select name="editRating">
                            <option value="0">0</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                        <br></br>
                        <textarea name="editComment" class="form-control" placeholder="Edit Review." required oninvalid="this.setCustomValidity('A Comment is required to submit.')" oninput="setCustomValidity('')"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" name="editReview">Edit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Report User Modal -->
    <div class="modal fade" id="userReportModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Report this user?</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="reportuser.php" method="post" id="reportComment">
                    <div class="modal-body">
                        <p>Are you sure you want to report this user?</p>
                        <p class="text-secondary"><small>Once this report is submitted, it cannot be changed.</small></p>
                        Category:&nbsp;
                        <select name="reportCategory">
                            <option value="inappropriate">Inappropriate</option>
                            <option value="spam">Spam</option>
                            <option value="self-promotional">Self-Promotional</option>
                            <option value="incorrect movie">Incorrect Movie</option>
                            <option value="spoiler">Spoiler</option>
                            <option value="other">Other</option>
                        </select>
                        <br></br>
                        <textarea name="reportComment" class="form-control" placeholder="Enter Explanation (Optional)."></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" name="report">Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12 col-md-8 col-lg-12">
                <?php
                echo "<h2>$title</h2>";
                echo "<hr></hr>";
                echo "<div class='row'>";
                echo "<div class='col-md-3'><img height='320' width ='220' src='data:image/jpeg;base64," . base64_encode($movieRow['Cover']) . "'/></div>";
                echo "<div class='col-md-9'>";
                echo "Release Date: <b>$releaseDate</b><br></br>";
                echo "Director: <b>$director</b><br></br>";
                echo "Posted By User: <b>$postedUsername</b><br></br>";
                echo "Genres: <b>$genres</b><br></br>";
                echo "Actors: <b>$actors</b><br></br>";
                echo "Average Rating: <b>$avgRating</b> ($userCounter User reviews)<br></br>";
                echo "<button class='btn btn-outline-primary btn-sm watchlistButton' type='button'>Add to Watchlist</button>";
                echo "</div></div>";
                ?>
                <hr>
                </hr>
                <div class="row">
                    <div class="col-md-12">
                        <form action="reviewsubmit.php" method="post" id="commentForm">
                            <div class="form-group">
                                Rating:&nbsp;
                                <select name="rating">
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <h4>Submit a Review</h4>
                                <textarea name="review" class="form-control" placeholder="Enter Review." required oninvalid="this.setCustomValidity('A Comment is required to submit.')" oninput="setCustomValidity('')"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary" name="submitReview">Submit Review</button><br></br>
                        </form>
                    </div>
                </div>
                <hr>
                </hr>
                <!-- PHP Code for retrieving comments -->
                <div class="col-md-12 container-fluid">
                    <?php
                    $resultReviews = mysqli_query($link, "SELECT Review_Date, Rating, Comment, User_ID FROM User_reviews WHERE Movie_ID='$movieID' ORDER BY Review_Date");

                    if ($resultReviews) {
                        while ($rowReviews = mysqli_fetch_assoc($resultReviews)) {
                            $userID = $rowReviews['User_ID'];
                            $resultUser = mysqli_query($link, "SELECT Username, User_Type FROM User WHERE User_ID='$userID'");
                            $rowUser = mysqli_fetch_assoc($resultUser);
                            $username = $rowUser['Username'];
                            $userType = $rowUser['User_Type'];

                            if (isset($_SESSION['username']) && isset($_SESSION['userID'])) {
                                if ($userType == 'P') {
                                    if ($_SESSION['userID'] == $userID) {
                                        // If User is logged in and they are Premium and it is their comment
                                        echo "<div class='border border-warning mt-4 container'>";
                                        echo "<h5 class='text-warning'>Posted by Premium User: $username (on $rowReviews[Review_Date] " . date_default_timezone_get() . ")</h5>";
                                        echo "<p>Rating: <b>$rowReviews[Rating]/5</b></p>";
                                        echo "<p><b>$rowReviews[Comment]</b></p>";
                                        echo "<button class='btn btn-outline-primary btn-sm' type='button' data-toggle='modal' data-target='#editReviewModal'>Edit</button>&nbsp&nbsp";
                                        echo "<button class='btn btn-outline-primary btn-sm' type='button' data-toggle='modal' data-target='#deleteReviewModal'>Delete</button>";
                                        echo "</div>";
                                    } else {
                                        // If User is logged in but it is not their comment and commenter is Premium
                                        echo "<div class='border border-warning mt-4 container'>";
                                        echo "<h5 class='text-warning'>Posted by Premium User: $username (on $rowReviews[Review_Date] " . date_default_timezone_get() . ")</h5>";
                                        echo "<p>Rating: <b>$rowReviews[Rating]/5</b></p>";
                                        echo "<p><b>$rowReviews[Comment]</b></p>";
                                        echo "<button class='btn btn-outline-primary btn-sm userReport' type='button' data-toggle='modal' data-target='#userReportModal' id='$userID'>Report</button>";
                                        echo "</div>";
                                    }
                                } else {
                                    if ($_SESSION['userID'] == $userID) {
                                        // If User is logged in and they are Non-Premium and it is their comment
                                        echo "<div class='border border-secondary mt-4 container'>";
                                        echo "<h5 class='text-secondary'>Posted by User: $username (on $rowReviews[Review_Date] " . date_default_timezone_get() . ")</h5>";
                                        echo "<p>Rating: <b>$rowReviews[Rating]/5</b></p>";
                                        echo "<p><b>$rowReviews[Comment]</b></p>";
                                        echo "<button class='btn btn-outline-primary btn-sm' type='button' data-toggle='modal' data-target='#editReviewModal'>Edit</button>&nbsp&nbsp";
                                        echo "<button class='btn btn-outline-primary btn-sm' type='button' data-toggle='modal' data-target='#deleteReviewModal'>Delete</button>";
                                        echo "</div>";
                                    } else {
                                        // If User is logged in but it is not their comment and commenter is Non-Premium
                                        echo "<div class='border border-secondary mt-4 container'>";
                                        echo "<h5 class='text-secondary'>Posted by User: $username (on $rowReviews[Review_Date] " . date_default_timezone_get() . ")</h5>";
                                        echo "<p>Rating: <b>$rowReviews[Rating]/5</b></p>";
                                        echo "<p><b>$rowReviews[Comment]</b></p>";
                                        echo "<button class='btn btn-outline-primary btn-sm userReport' type='button' data-toggle='modal' data-target='#userReportModal' id='$userID'>Report</button>";
                                        echo "</div>";
                                    }
                                }
                            } else {
                                if ($userType == 'P') {
                                    // If User is not logged in and commenter is Premium
                                    echo "<div class='border border-warning mt-4 container'>";
                                    echo "<h5 class='text-warning'>Posted by Premium User: $username (on $rowReviews[Review_Date] " . date_default_timezone_get() . ")</h5>";
                                    echo "<p>Rating: <b>$rowReviews[Rating]/5</b></p>";
                                    echo "<p><b>$rowReviews[Comment]</b></p>";
                                    echo "</div>";
                                } else {
                                    // If User is not logged in and commenter is Non-Premium
                                    echo "<div class='border border-secondary mt-4 container'>";
                                    echo "<h5 class='text-secondary'>Posted by User: $username (on $rowReviews[Review_Date] " . date_default_timezone_get() . ")</h5>";
                                    echo "<p>Rating: <b>$rowReviews[Rating]/5</b></p>";
                                    echo "<p><b>$rowReviews[Comment]</b></p>";
                                    echo "</div>";
                                }
                            }
                            mysqli_free_result($resultUser);
                        }
                        mysqli_free_result($resultReviews);
                        mysqli_close($link);
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <br></br>
</body>

</html>