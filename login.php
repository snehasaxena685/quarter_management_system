<?php
session_start();
if (isset($_SESSION['username'])) {
    header("Location: staff_sections/staff_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>CFTRI Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
  background: #f4f7fb;
  font-family: 'Segoe UI';
}
.card {
  max-width: 400px;
  margin: 100px auto;
  padding: 25px;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.btn-primary {
  background-color: #0b2341;
  border: none;
}
</style>
</head>
<body>
<div class="card">
  <h4 class="text-center mb-3 text-primary">CFTRI Login</h4>
  <form method="POST" action="validate_login.php">
    <div class="mb-3">
      <label class="form-label">Username / Employee No</label>
      <input type="text" name="username" class="form-control" required placeholder="Enter your Employee No or Admin ID">
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required placeholder="Enter Password">
      <div class="form-text text-muted small">Default for staff: last 4 digits of Emp No + first 3 letters of name</div>
    </div>
    <button type="submit" class="btn btn-primary w-100">Login</button>
  </form>
</div>
</body>
</html>