<?php
session_start();
if(!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Staff Dashboard | CFTRI Quarter Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
body {
  background: #eef1f6;
  font-family: 'Segoe UI', 'Open Sans', sans-serif;
  overflow-x: hidden;
}

/* Navbar */
.navbar {
  background: linear-gradient(90deg, #0b2341 0%, #1c437b 100%);
  padding: 12px 25px;
  color: #fff;
  box-shadow: 0 4px 10px rgba(0,0,0,0.25);
}
.navbar-brand {
  font-weight: 600;
  font-size: 20px;
  color: #fff !important;
}
.navbar-text {
  color: #dbe4f5 !important;
  font-weight: 500;
}
.navbar-text a {
  color: #fff !important;
  text-decoration: none;
  margin-left: 10px;
  font-weight: 600;
}
.navbar-text a:hover {
  text-decoration: underline;
}

/* Page Header */
.page-header {
  text-align: center;
  margin-top: 30px;
  margin-bottom: 40px;
  animation: fadeIn 1.2s ease;
}
.page-header h3 {
  color: #0b2341;
  font-weight: 700;
}
.page-header p {
  color: #6c757d;
}

/* Dashboard Cards */
.card {
  border: none;
  border-radius: 14px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  padding: 25px 15px;
  transition: all 0.3s ease;
  background: #fff;
  height: 100%;
  position: relative;
  overflow: hidden;
}
.card::before {
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, rgba(255,255,255,0.2), rgba(255,255,255,0));
  transition: left 0.6s ease;
}
.card:hover::before {
  left: 100%;
}
.card:hover {
  transform: translateY(-6px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}
.card i {
  font-size: 2.5rem;
  margin-bottom: 15px;
  transition: color 0.3s ease;
}
.card h6 {
  color: #0b2341;
  font-weight: 600;
}
.card p {
  color: #6c757d;
  font-size: 0.9rem;
}
.btn-sm {
  border-radius: 25px;
  font-weight: 600;
  padding: 6px 18px;
}

/* Footer */
.footer {
  background: #0b2341;
  color: #fff;
  padding: 15px;
  text-align: center;
  margin-top: 50px;
  font-size: 0.9rem;
  box-shadow: 0 -3px 8px rgba(0,0,0,0.15);
}

/* Animations */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
</head>

<body>
<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">
      <i class="fa-solid fa-building-columns me-2"></i>CFTRI Staff Dashboard
    </a>
    <div class="navbar-text ms-auto">
      Welcome, <strong><?= htmlspecialchars($_SESSION['username']); ?></strong> |
      <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
  </div>
</nav>

<div class="container">
  <div class="page-header">
    <h3>Welcome to CFTRI Quarter Management System</h3>
    <p>Manage your quarters, applications, and reports — all in one place.</p>
  </div>

  <div class="row g-4 justify-content-center">

    <!-- View Quarters -->
    <div class="col-md-3 col-sm-6">
      <div class="card text-center">
        <i class="fa-solid fa-house text-primary"></i>
        <h6>View Quarters</h6>
        <p>Check available quarters and details.</p>
        <a href="quarters_staff.php" target="contentFrame" class="btn btn-outline-primary btn-sm">Open</a>
      </div>
    </div>

   
    <!-- Apply / Upgrade -->
    <div class="col-md-3 col-sm-6">
      <div class="card text-center">
        <i class="fa-solid fa-file-pen text-info"></i>
        <h6>Apply / Upgrade</h6>
        <p>Submit new or upgrade your existing quarter application.</p>
        <a href="staff_sections/apply_quarter.php" class="btn btn-outline-info btn-sm">Apply</a>
      </div>
    </div>

    <!-- Application Status -->
    <div class="col-md-3 col-sm-6">
      <div class="card text-center">
        <i class="fa-solid fa-list-check text-warning"></i>
        <h6>Application Status</h6>
        <p>Track the progress of your submitted applications.</p>
        <a href="staff_sections/application_status.php" class="btn btn-outline-warning btn-sm">Track</a>
      </div>
    </div>

   
</div>

<div class="footer">
  <small>© 2025 CSIR-CFTRI | Government of India | Quarter Management Portal</small>
</div>

</body>
</html>
