<?php
// Made by Andy Lee

// Gets current resumed User session
session_start();
// Destroys User session
session_destroy();

header('location: moviereview.php');
?>