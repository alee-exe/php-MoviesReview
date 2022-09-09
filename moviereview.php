<!DOCTYPE HTML>
<html lang="en">
<!-- Made by Andy Lee-->
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" href="movie.ico" type="image">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha256-pasqAKBDmFT4eHoN2ndd6lN370kFiGUFyTiUHWhU7k8=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  <script>
    $(document).ready(function() {
      // Function to edit the watchlist name and desc when edit button is clicked
      $('.watchlistButton').click(function() {
        window.location = 'watchlistedit.php';
      });
    });
  </script>
  <title>Movie Review Website</title>
</head>

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

// Checks if username and userID session variables are set for the rest of the session
// If they are set, change the website page display using PHP
if (isset($_SESSION['username']) && isset($_SESSION['userID'])) {
  $username = $_SESSION['username'];
  $userID = $_SESSION['userID'];

  $resultUser = mysqli_query($link, "SELECT * FROM User Where Username='$username' && User_ID='$userID'");
  $userArray = mysqli_fetch_assoc($resultUser);
  $listName = $userArray['List_Name'];
  $listDesc = $userArray['List_Description'];
  $userType = $userArray['User_Type'];
  $userEmail = $userArray['Email'];

  if ($userType == "P") {
    $resultExpireMember = mysqli_query($link, "SELECT Expire_Date FROM Premium WHERE User_ID='$userID'");
    $expireMemberDateTime = mysqli_fetch_assoc($resultExpireMember)['Expire_Date'];
    $isMember = "<a href='member.php'>Membership</a>: <b>You are currently a Premium Member (Ends: $expireMemberDateTime)</b><br></br>";
    mysqli_free_result($resultExpireMember);
  } else if ($userType == "T") {
    $resultExpireTrial = mysqli_query($link, "SELECT Expire_Date FROM Trial WHERE User_ID='$userID'");
    $expireTrialDateTime = mysqli_fetch_assoc($resultExpireTrial)['Expire_Date'];
    $isMember = "<a href='member.php'>Membership</a>: <b>You are currently on a Membership Trial (Ends: $expireTrialDateTime)</b><br></br>";
    mysqli_free_result($resultExpireTrial);
  } else {
    $isMember = "<a href='member.php'>Membership</a>: <b>You are currently not a Member. <a href='member.php'>Join today!</a></b><br></br>";
  }

  if ($listName == null) {
    $listName = "$username's Watchlist";
  }

  if ($listDesc == null) {
    $listDesc = "Enter Description.";
  }

  mysqli_free_result($resultUser);
}

?>

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
        echo "<span class='navbar-text text-warning'><h2> Welcome, $username</h2></span>";
      } else {
        echo "<span class='navbar-text'><h2> Welcome, $username</h2></span>";
      }
    }
    ?>
  </nav>

  <div class="container mt-4">
    <div class="row">
      <div class="col-12 col-md-8 col-lg-12">
        <ul class="nav nav-tabs" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#movies" role="tab" aria-controls="movies" aria-selected="true">Movies</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#reviews" role="tab" aria-cntrols="reviews" aria-selected="false">My Reviews</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#account" role="tab" aria-cntrols="account" aria-selected="false">Account</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#watchlist" role="tab" aria-cntrols="watchlist" aria-selected="false">Watchlist</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#help" role="tab" aria-cntrols="help" aria-selected="false">Help</a>
          </li>
        </ul>
        <div class="tab-content mt-4">

          <div class="tab-pane active" id="movies" role="tabpanel" aria-labelledby="movies-tab">
            <h4>Movie List</h4>
            <hr>
            </hr>
            <h5>Don't see a Movie that you like? Submit one <a href="moviesubmit.php">here!</a></h5>
            <hr>
            </hr>
            <div class="container-fluid">
              <div class="row">
                <?php
                $resultMovie = mysqli_query($link, "SELECT Movie_ID, Title, Cover FROM Movie ORDER BY Title");
                while ($rowMovie = mysqli_fetch_assoc($resultMovie)) {
                  $thisMovieID = $rowMovie['Movie_ID'];
                  echo "<div class='col-md-2 text-center'>";
                  echo "<a href='moviepage.php?movieid=$thisMovieID'>";
                  echo "<img height='200' width ='150' src='data:image/jpeg;base64," . base64_encode($rowMovie['Cover']) . "'/>";
                  echo "</a>";
                  echo "<br></br><a href='moviepage.php?movieid=$thisMovieID'>$rowMovie[Title]</a><br></br></div>";
                }
                mysqli_free_result($resultMovie);
                ?>
              </div>
            </div>
          </div>

          <div class="tab-pane" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
            <?php
            if (!isset($_SESSION['username'])) {
              echo "<a href='login.php'>Login</a> to see your reviews.";
            } else {
              $resultMyReviews = mysqli_query($link, "SELECT User_reviews.Comment, User_reviews.Rating, User_reviews.Review_Date, Movie.Title, Movie.Movie_ID FROM User_reviews INNER JOIN Movie ON User_reviews.Movie_ID = Movie.Movie_ID WHERE User_reviews.User_ID='$userID' ORDER BY User_reviews.Review_Date");
              echo "<h4>My Reviews</h4><hr></hr>";
              while ($rowReview = mysqli_fetch_assoc($resultMyReviews)) {
                $thisMovieID = $rowReview['Movie_ID'];
                if ($userType == "P") {
                  echo "<div class='border border-warning mt-4 container'>";
                  echo "<h5>Reviewed <b><a href='moviepage.php?movieid=$thisMovieID'>$rowReview[Title]</b></a> on $rowReview[Review_Date] " . date_default_timezone_get() . "</h5>";
                  echo "<h5>Rating given: <b>$rowReview[Rating]/5</b></h5>";
                  echo "<h5>Review: <b>$rowReview[Comment]</b></h5>";
                  echo "</div>";
                } else {
                  echo "<div class='border border-secondary mt-4 container'>";
                  echo "<h5>Reviewed <b><a href='moviepage.php?movieid=$thisMovieID'>$rowReview[Title]</b></a> on $rowReview[Review_Date] " . date_default_timezone_get() . "</h5>";
                  echo "<h5>Rating given: <b>$rowReview[Rating]/5</b></h5>";
                  echo "<h5>Review: <b>$rowReview[Comment]</b></h5>";
                  echo "</div>";
                }
              }
              if (mysqli_num_rows($resultMyReviews) == 0) {
                echo "<h5>You have no reviews!</h5>";
              }
            }
            ?>
          </div>

          <div class="tab-pane" id="account" role="tabpanel" aria-labelledby="account-tab">
            <?php
            if (!isset($_SESSION['username'])) {
              echo "<a href='login.php'>Login</a> to see your account.";
            } else {
              echo "<h4>Your Account</h4><hr></hr>";
              echo "Username: <b>$username</b><br></br>";
              echo "Email: <b>$userEmail</b><br></br>";
              echo $isMember;
            }
            ?>
          </div>

          <div class="tab-pane" id="watchlist" role="tabpanel" aria-labelledby="watchlist-tab">
            <?php
            if (!isset($_SESSION['username'])) {
              echo "<a href='login.php'>Login</a> to see your watchlist.";
            } else {
              echo "<div style='display:flex;'><h4>$listName&nbsp&nbsp</h4>";
              echo "<button class='btn btn-outline-primary btn-sm watchlistButton' type='button'>Edit</button></div><hr></hr>";
              echo "<div style='display:flex;'><h4 class = 'text-muted'>$listDesc&nbsp&nbsp</h4>";
              echo "<button class='btn btn-outline-primary btn-sm watchlistButton' type='button'>Edit</button></div><hr></hr>";
              $resultMyWatchlist = mysqli_query($link, "SELECT Watchlist_adds.Movie_ID, Watchlist_adds.Add_Date, Movie.Title FROM Watchlist_adds INNER JOIN Movie ON Watchlist_adds.Movie_ID = Movie.Movie_ID WHERE Watchlist_adds.User_ID='$userID' ORDER BY Add_Date");
              // $count variable to number the movies added to watchlist
              $count = 0;
              while ($rowWatchlist = mysqli_fetch_assoc($resultMyWatchlist)) {
                $count++;
                echo "<div class='container'>";
                echo "<h5>$count. <a href='moviepage.php?movieid=$rowWatchlist[Movie_ID]'>$rowWatchlist[Title]</a> (Added on: $rowWatchlist[Add_Date] " . date_default_timezone_get() . ")</h5>";
                echo "</div>";
              }
              if (mysqli_num_rows($resultMyWatchlist) == 0) {
                echo "<h5>You have no movies in your watchlist!</h5>";
              }
            }
            ?>
          </div>

          <div class="tab-pane" id="help" role="tabpanel" aria-labelledby="help-tab">
            <h5>What is "Movie Review Website"?</h5>
            <hr>
            </hr>
            Movie Review Website is a dynamic PHP website that uses MySQL to manage the database system hosted by XAMPP. PHP is used to connect to the database where Users can register their accounts and login using sessions. Users can then store information into the database such as their reviews on movies, their watchlist, profile information and etc.
            <br></br>
            This website is dynamically generated depending on the User's session and is intended to be run by users for users. Users can submit movies to the database and other users can review them. This way, developers of the website won't need to manually add new content to the database when new movies are released.
            <br></br>
            However, a problem that may occur is users inappropriately submitting "non-existent" movies or leaving inappropriate reviews. A future solution would be to implement a monitoring system of movies uploaded before they are uploaded into the database and to implement moderators as a new user subtype to manage reviews.
            <hr>
            </hr>
            <h5>Where do I start?</h5>
            <hr>
            </hr>
            As the User and the Admin, you can start anywhere with this website. I would highly recommend registering an account first and start interacting with the website. But make sure all the PHP files are located inside the htdocs folder as all the PHP files use relative paths which point to this folder.
            <br></br>
            Make sure both the Apache and MySQL modules are running using XAMPP. Apache is used for the web server and MySQL is used for the database server. You can see and manually make changes to the database using <a href="phpmyadmin">phpMyAdmin</a> (which is included with XAMPP).
            <hr>
            </hr>
            <h5>Who made this website?</h5>
            <hr>
            </hr>
            This website was made by me, Andy Lee! With the help of many online resources and much time spent coding, I've made my first simple interactive website using PHP and MySQL (with local servers) for my university coursework.
            <br></br>
            I hope you enjoy using this website as much as I had spent making it, Andrew and Yongxin!
          </div>
        </div>
      </div>
    </div>
  </div>

  <br></br>
  <footer class="page-footer font-small fixed-bottom">
    <div class="text-center py-3"> @ 2019-2020:
      <a class="text-warning"> Made by Andy Lee</a>
    </div>
  </footer>
</body>

<?php
mysqli_close($link);
?>

</html>