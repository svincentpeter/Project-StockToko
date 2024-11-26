<?php
$servername = "localhost";
$username = "peter";
$password = "peter123";
$dbname = "omah_ban"; // Perbaiki nama database

// Membuat koneksi
$conn = new mysqli($servername, $username, $password);

// Memeriksa koneksi
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Memilih database
if (!$conn->select_db($dbname)) {
  die("Database selection failed: " . $conn->error);
}
?>
