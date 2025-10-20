<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard | CFTRI Quarter Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
      body {
          font-family: 'Segoe UI', 'Open Sans', sans-serif;
          background-color: #eef2f8;
          overflow-x: hidden;
      }

      /* Navbar */
      .navbar {
          background: linear-gradient(90deg, #0b2341 0%, #1e4a85 100%);
          color: white;
          padding: 10px 25px;
          box-shadow: 0 3px 10px rgba(0,0,0,0.25);
          display: flex;
          align-items: center;
          justify-content: space-between;
      }
      .navbar .brand {
          display: flex;
          align-items: center;
          font-weight: 600;
          font-size: 20px;
      }
      .navbar img {
          height: 45px;
          margin-right: 12px;
          animation: fadeIn 1.2s ease;
      }
      .navbar a {
          color: #fff !important;
          text-decoration: none;
          font-weight: 500;
          transition: all 0.3s;
      }
      .navbar a:hover {
          color: #cce3ff !important;
      }

      /* Sidebar */
      .sidebar {
          background: linear-gradient(180deg, #0f264a, #1c437b);
          min-height: 100vh;
          color: #cdd8f0;
          padding-top: 20px;
          box-shadow: 2px 0 10px rgba(0,0,0,0.2);
          animation: slideIn 0.8s ease;
      }
      .sidebar h5 {
          text-align: center;
          font-weight: 600;
          color: #ffffff;
          margin-bottom: 20px;
      }
      .sidebar a {
          color: #cdd8f0;
          display: flex;
          align-items: center;
          gap: 10px;
          padding: 12px 22px;
          text-decoration: none;
          font-size: 15px;
          border-left: 4px solid transparent;
          transition: all 0.3s ease;
      }
      .sidebar a:hover, .sidebar a.active {
          background: rgba(255,255,255,0.08);
          color: #ffffff;
          border-left: 4px solid #4da3ff;
          transform: translateX(4px);
      }
      .sidebar i {
          width: 20px;
          text-align: center;
      }

      /* Main Dashboard */
      .main-content {
          padding: 25px;
          background: #f8f9fb;
          min-height: 100vh;
          animation: fadeIn 1.2s ease;
      }
      .welcome {
          font-size: 22px;
          font-weight: 600;
          color: #0b2341;
          margin-bottom: 20px;
      }

      /* Dashboard Summary Cards */
      .stats-row {
          display: flex;
          flex-wrap: wrap;
          gap: 20px;
          margin-bottom: 25px;
      }
      .stat-card {
          flex: 1;
          min-width: 230px;
          background: #fff;
          border-radius: 14px;
          padding: 22px;
          box-shadow: 0 4px 14px rgba(0,0,0,0.08);
          transition: all 0.3s ease;
          position: relative;
          overflow: hidden;
          animation: fadeUp 0.8s ease;
      }
      .stat-card:hover {
          transform: translateY(-6px);
          box-shadow: 0 10px 25px rgba(0,0,0,0.15);
      }
      .stat-title {
          font-size: 14px;
          color: #666;
          text-transform: uppercase;
      }
      .stat-value {
          font-size: 26px;
          font-weight: 700;
          color: #0b2341;
      }
      .stat-icon {
          font-size: 30px;
          background: linear-gradient(135deg, #1e4a85, #00bfff);
          -webkit-background-clip: text;
          -webkit-text-fill-color: transparent;
      }

      /* Animations */
      @keyframes fadeIn {
          from { opacity: 0; transform: translateY(10px); }
          to { opacity: 1; transform: translateY(0); }
      }
      @keyframes slideIn {
          from { transform: translateX(-50px); opacity: 0; }
          to { transform: translateX(0); opacity: 1; }
      }
      @keyframes fadeUp {
          from { opacity: 0; transform: translateY(30px); }
          to { opacity: 1; transform: translateY(0); }
      }

      /* Footer */
      .footer {
          text-align: center;
          color: #666;
          padding: 15px;
          font-size: 13px;
          border-top: 1px solid #ddd;
          background: #f4f6fb;
      }
  </style>
</head>
<body>
  <!-- Navbar -->
  <div class="navbar">
    <div class="brand">
      <img src="./assets/cftri_logo.jpg" alt="CFTRI Logo" >
      CFTRI Quarter Management System
    </div>
    <div>
      <span>Welcome, <strong><?php echo $_SESSION['username']; ?></strong></span>
      <a href="logout.php" class="btn btn-sm btn-outline-light ms-3">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
      </a>
    </div>
  </div>

  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-md-2 sidebar p-0">
        <h5 class="mt-3 mb-3">Admin Panel</h5>
        <a href="admin_sections/Qquarters.php" target="contentFrame">
          <i class="fa-solid fa-building"></i> Quarters
        </a>
        <a href="admin_sections/employee.php" target="contentFrame">
          <i class="fa-solid fa-users"></i> Employees
        </a>
        <a href="admin_sections/occupation_vacation.php" target="contentFrame">
          <i class="fa-solid fa-house-user"></i> Occupation/Vacation
        </a>
        <a href="admin_sections/quarters_history.php" target="contentFrame">
          <i class="fa-solid fa-house-user"></i> Quarters-Archive
        </a>
        <a href="admin_sections/applications.php" target="contentFrame" class="active">
          <i class="fa-solid fa-file-circle-check"></i> View Quarter Applications
        </a>
        <a href="admin_sections/user.php" target="contentFrame">
          <i class="fa-solid fa-users"></i> Create Users
        </a>
        <hr style="border-color: rgba(255,255,255,0.2); margin: 15px;">
        <a href="logout.php">
          <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
        </a>
      </div>

      <!-- Main Dashboard -->
      <div class="col-md-10 main-content">
        <div class="welcome">Dashboard Overview</div>

        <!-- Dashboard Summary Cards -->
        <div class="stats-row">
          <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="stat-title"><a href="admin_sections/Qquarters.php" style="color: inherit; text-decoration: none;">Total Quarters</a></div>
                <div class="stat-value">315</div>
              </div>
              <i class="fa-solid fa-building stat-icon"></i>
            </div>
          </div>
          <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="stat-title"><a href="admin_sections/occupation_vacation.php" style="color: inherit; text-decoration: none;">Occupied</a></div>
                <div class="stat-value text-success">270</div>
              </div>
              <i class="fa-solid fa-user-check stat-icon"></i>
            </div>
          </div>
          <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="stat-title"><a href="admin_sections/occupation_vacation.php" style="color: inherit; text-decoration: none;">Vacant</a></div>
                <div class="stat-value text-danger">45</div>
              </div>
              <i class="fa-solid fa-door-open stat-icon"></i>
            </div>
          </div>

          <!-- 🆕 Quarter Applications Summary -->
          <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="stat-title"><a href="admin_sections/applications.php" style="color: inherit; text-decoration: none;">Applications</a></div>
                <div class="stat-value text-primary">24</div>
              </div>
              <i class="fa-solid fa-file-circle-check stat-icon"></i>
            </div>
          </div>
        </div>

        <!-- Optional: Embedded IFrame for Dynamic Section Loading -->
        <iframe name="contentFrame" style="width: 100%; height: 70vh; border: none; border-radius: 12px; box-shadow: 0 3px 15px rgba(0,0,0,0.1);"></iframe>
      </div>
    </div>
  </div>

  <div class="footer">
    © 2025 CSIR–CFTRI | Quarter Management System | Developed for Administrative Use
  </div>
</body>
</html>
