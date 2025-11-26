<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'student'){
  header("Location: index.php");
  exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Student Dashboard</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="dashboard">
    <h2>Welcome, <?= $_SESSION['username']; ?> (Student)</h2>
    <p>This is the Student Dashboard.</p>
    <a href="logout.php">Logout</a>
  </div>
</body>
</html>