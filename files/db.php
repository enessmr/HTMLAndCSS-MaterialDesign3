<?php
$servername = 'localhost';
$username = 'root';
$password = 'Nono';
$dbname = 'd'; // Replace with your actual database name

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die('Could not connect to MySQL Server: ' . mysqli_error());
}
?>
