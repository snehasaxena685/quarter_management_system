<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// -------- FETCH LIVE COUNTS --------

// Total quarters
$total_quarters = $conn->query("SELECT COUNT(*) AS total FROM quarters_admin")->fetch_assoc()['total'] ?? 0;

// Occupied quarters
$occupied_quarters = $conn->query("SELECT COUNT(*) AS total FROM quarters_admin WHERE status='occupied'")->fetch_assoc()['total'] ?? 0;

// Vacant quarters
$vacant_quarters = $conn->query("SELECT COUNT(*) AS total FROM quarters_admin WHERE status='vacant'")->fetch_assoc()['total'] ?? 0;

// Applications (optional)
$applications = 0;
if ($conn->query("SHOW TABLES LIKE 'quarter_applications'")->num_rows > 0) {
    $applications = $conn->query("SELECT COUNT(*) AS total FROM quarter_applications")->fetch_assoc()['total'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard | CFTRI Quarters Management</title>
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
    padding: 12px 25px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.25);
    display: flex;
    align-items: center;
    justify-content: space-between;
    z-index: 1000;
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
    border-radius: 6px;
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
    transition: all 0.4s ease;
  }
  .sidebar h5 {
    text-align: center;
    font-weight: 600;
    color: #ffffff;
    margin-bottom: 20px;
    letter-spacing: 0.5px;
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
    padding: 25px 40px;
    background: #f8f9fb;
    min-height: 100vh;
    animation: fadeIn 1.2s ease;
    position: relative;
  }
  .welcome {
    font-size: 24px;
    font-weight: 700;
    color: #0b2341;
    margin-bottom: 30px;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.08);
    letter-spacing: 0.5px;
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
    min-width: 240px;
    background: linear-gradient(145deg, #ffffff, #f1f4f9);
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.08);
    transition: all 0.4s ease;
    cursor: pointer;
    overflow: hidden;
    position: relative;
  }
  .stat-card::after {
    content: "";
    position: absolute;
    top: 0; left: -100%;
    width: 100%; height: 100%;
    background: rgba(255,255,255,0.25);
    transition: 0.4s;
  }
  .stat-card:hover::after {
    left: 100%;
  }
  .stat-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
  }
  .stat-title {
    font-size: 15px;
    color: #666;
    text-transform: uppercase;
  }
  .stat-value {
    font-size: 28px;
    font-weight: 700;
    color: #0b2341;
  }
  .stat-icon {
    font-size: 34px;
    background: linear-gradient(135deg, #1e4a85, #00bfff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  /* Iframe */
  iframe {
    width: 100%;
    height: 78vh;
    border: none;
    background: transparent;
    display: none;
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.5s ease, transform 0.5s ease;
  }

  iframe.active {
    display: block;
    opacity: 1;
    transform: translateY(0);
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

  /* Animations */
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }
  @keyframes slideIn {
    from { transform: translateX(-50px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
  }

  /* Responsive */
  @media (max-width: 768px) {
    .stats-row { flex-direction: column; }
  }
</style>
</head>

<body>
  <!-- Navbar -->
  <div class="navbar">
    <div class="brand">
      <img src="./assets/cftri_logo.jpg" alt="CFTRI Logo">
      CFTRI Quarters Management System
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
        <a href="#" onclick="showDashboard()" class="active">
          <i class="fa-solid fa-chart-line"></i> Dashboard
        </a>
        <a href="#" onclick="openSection('admin_sections/Qquarters.php')">
          <i class="fa-solid fa-building"></i> Quarters
        </a>
        <a href="#" onclick="openSection('admin_sections/employees.php')">
          <i class="fa-solid fa-users"></i> Employees
        </a>
        <a href="#" onclick="openSection('admin_sections/occupation_vacation.php')">
          <i class="fa-solid fa-house-user"></i> Occupation/Vacation
        </a>
        <a href="#" onclick="openSection('admin_sections/quarters_history.php')">
          <i class="fa-solid fa-clock-rotate-left"></i> Quarters-Archive
        </a>
        <!-- ✅ Updated Link -->
        <a href="#" onclick="openSection('admin_sections/applications_hub.php')">
          <i class="fa-solid fa-file-circle-check"></i> Applications
        </a>
        <a href="#" onclick="openSection('admin_sections/sync_employees.php')">
          <i class="fa-solid fa-user-gear"></i> Create Login Accounts
        </a>
        <a href="#" onclick="openSection('admin_sections/admin_manual.php')">
          <i class="fa-solid fa-screwdriver-wrench"></i> Admin Manual
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
          <div class="stat-card" onclick="openSection('admin_sections/Qquarters.php')">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="stat-title">Total Quarters</div>
                <div class="stat-value"><?php echo $total_quarters; ?></div>
              </div>
              <i class="fa-solid fa-building stat-icon"></i>
            </div>
          </div>

          <div class="stat-card" onclick="openSection('admin_sections/occupation_vacation.php', 'occupied')">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="stat-title">Occupied</div>
                <div class="stat-value text-success"><?php echo $occupied_quarters; ?></div>
              </div>
              <i class="fa-solid fa-user-check stat-icon"></i>
            </div>
          </div>

          <div class="stat-card" onclick="openSection('admin_sections/occupation_vacation.php', 'vacant')">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="stat-title">Vacant</div>
                <div class="stat-value text-danger"><?php echo $vacant_quarters; ?></div>
              </div>
              <i class="fa-solid fa-door-open stat-icon"></i>
            </div>
          </div>

          <div class="stat-card" onclick="openSection('admin_sections/applications_hub.php')">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="stat-title">Applications</div>
                <div class="stat-value text-primary"><?php echo $applications; ?></div>
              </div>
              <i class="fa-solid fa-file-circle-check stat-icon"></i>
            </div>
          </div>
        </div>

        <iframe name="contentFrame" id="contentFrame"></iframe>
      </div>
    </div>
  </div>

  <div class="footer">
    © 2025 CSIR–CFTRI | Quarters Management System | Developed By ITS & CS Department
  </div>

  <script>
    const iframe = document.getElementById('contentFrame');
    const statsRow = document.querySelector('.stats-row');
    const welcome = document.querySelector('.welcome');
    const sidebarLinks = document.querySelectorAll('.sidebar a');

    function openSection(url, status = '') {
      statsRow.style.display = 'none';
      welcome.style.display = 'none';
      iframe.classList.add('active');

      if(status) {
          iframe.src = `${url}?status=${status}`;
      } else {
          iframe.src = url;
      }

      sidebarLinks.forEach(link => link.classList.remove('active'));
      event.target.closest('a').classList.add('active');
    }

    function showDashboard() {
      iframe.classList.remove('active');
      setTimeout(() => {
        iframe.src = '';
        statsRow.style.display = 'flex';
        welcome.style.display = 'block';
      }, 300);

      sidebarLinks.forEach(link => link.classList.remove('active'));
      document.querySelector('.sidebar a:first-child').classList.add('active');
    }
  </script>
</body>
</html>
