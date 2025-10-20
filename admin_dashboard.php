<?php
session_start();
require_once "db_connect.php"; // 🔹 Make sure this file has your DB connection

// Security check — only admin allowed
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Fetch counts dynamically
$total_quarters = $occupied = $vacant = 0;
$result = $conn->query("SELECT 
    COUNT(*) AS total,
    SUM(CASE WHEN status='Occupied' THEN 1 ELSE 0 END) AS occupied,
    SUM(CASE WHEN status='Vacant' THEN 1 ELSE 0 END) AS vacant
    FROM quarters");

if ($result && $row = $result->fetch_assoc()) {
    $total_quarters = $row['total'];
    $occupied = $row['occupied'];
    $vacant = $row['vacant'];
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
          font-family:'Segoe UI','Open Sans',sans-serif;
          background-color:#eef1f6;
          overflow-x:hidden;
          display:flex;
          flex-direction:column;
          min-height:100vh;
      }

      /* Navbar */
      .navbar {
          background:linear-gradient(90deg,#0b2341 0%,#18467a 100%);
          color:white;
          padding:10px 25px;
          box-shadow:0 3px 10px rgba(0,0,0,0.25);
          display:flex;
          align-items:center;
          justify-content:space-between;
          flex-wrap:wrap;
      }
      .navbar .brand {
          display:flex;
          align-items:center;
          font-weight:600;
          font-size:20px;
      }
      .navbar img {
          height:45px;
          margin-right:12px;
      }

      /* Sidebar */
      .sidebar {
          background:linear-gradient(180deg,#0f264a,#1c437b);
          min-height:100vh;
          color:#cdd8f0;
          padding-top:20px;
          box-shadow:2px 0 10px rgba(0,0,0,0.2);
      }
      .sidebar h5 {
          text-align:center;
          font-weight:600;
          color:#ffffff;
          margin-bottom:20px;
      }
      .sidebar a {
          color:#cdd8f0;
          display:flex;
          align-items:center;
          gap:10px;
          padding:12px 22px;
          text-decoration:none;
          font-size:15px;
          border-left:4px solid transparent;
          transition:all 0.3s ease;
      }
      .sidebar a:hover {
          background:rgba(255,255,255,0.08);
          color:#ffffff;
          border-left:4px solid #4da3ff;
          transform:translateX(4px);
      }

      /* Main content */
      .main-content {
          flex:1;
          padding:25px;
          background:#f8f9fb;
          animation:fadeIn 1s ease;
      }
      .welcome {
          font-size:22px;
          font-weight:600;
          color:#0b2341;
          margin-bottom:20px;
      }

      /* Cards */
      .stats-row {
          display:flex;
          flex-wrap:wrap;
          gap:20px;
          margin-bottom:25px;
      }
      .stat-card {
          flex:1;
          min-width:220px;
          background:#fff;
          border-radius:12px;
          padding:20px;
          box-shadow:0 4px 12px rgba(0,0,0,0.08);
          transition:all 0.3s ease;
          position:relative;
          overflow:hidden;
      }
      .stat-card:hover {
          transform:translateY(-4px);
          box-shadow:0 8px 18px rgba(0,0,0,0.12);
      }
      .stat-title {font-size:14px;color:#666;text-transform:uppercase;}
      .stat-value {font-size:26px;font-weight:700;color:#0b2341;}
      .stat-icon {font-size:28px;color:#1b4b91;}

      iframe {
          width:100%;
          height:70vh;
          border:none;
          background:#fff;
          border-radius:10px;
          box-shadow:0 2px 8px rgba(0,0,0,0.1);
      }

      /* Footer */
      footer {
          background:linear-gradient(90deg,#0b2341 0%,#173a6b 100%);
          color:#e4ebf8;
          text-align:center;
          padding:15px 10px;
          font-size:14px;
          letter-spacing:0.3px;
          position:relative;
      }
      footer::before {
          content:"";
          position:absolute;
          top:-10px;
          left:0;
          width:100%;
          height:10px;
          background:linear-gradient(90deg,#4da3ff33,#ffffff22,#4da3ff33);
          clip-path:polygon(0 100%,100% 0,100% 100%);
      }
      footer a {
          color:#cce3ff;
          text-decoration:none;
      }
      footer a:hover {
          text-decoration:underline;
          color:#fff;
      }

      /* Animations */
      @keyframes fadeIn {
          from{opacity:0;transform:translateY(10px);}
          to{opacity:1;transform:translateY(0);}
      }

      /* Responsive Fix */
      @media(max-width:768px){
          .sidebar{min-height:auto;}
          iframe{height:60vh;}
      }
  </style>
</head>
<body>
  <!-- Navbar -->
  <div class="navbar">
    <div class="brand">
      <img src="https://cdn.universitykart.com/Content/upload/admin/wx5wcjk1.lpd.jfif" alt="CFTRI Logo">
      CFTRI Quarter Management System
    </div>
    <div>
      <span>Welcome, <strong><?= htmlspecialchars($_SESSION['username']); ?></strong></span>
      <a href="logout.php" class="btn btn-sm btn-outline-light ms-3">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
      </a>
    </div>
  </div>

  <div class="container-fluid flex-grow-1">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-md-2 sidebar p-0">
        <h5 class="mt-3 mb-3">Admin Panel</h5>
        <a href="admin_sections/quarters.php" target="contentFrame"><i class="fa-solid fa-building"></i> Quarters</a>
        <a href="admin_sections/employees.php" target="contentFrame"><i class="fa-solid fa-users"></i> Employees</a>
        <a href="admin_sections/admin_manage_users.php" target="contentFrame"><i class="fa-solid fa-user-gear"></i> Manage Users</a>
        <a href="admin_sections/occupation_vacation.php" target="contentFrame"><i class="fa-solid fa-house-user"></i> Occupation/Vacation</a>
        <hr style="border-color:rgba(255,255,255,0.2);margin:15px;">
        <a href="logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
      </div>

      <!-- Main Dashboard -->
      <div class="col-md-10 main-content">
        <div class="welcome">Dashboard Overview</div>
        <div class="stats-row">
          <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="stat-title">Total Quarters</div>
                <div class="stat-value"><?= $total_quarters; ?></div>
              </div>
              <i class="fa-solid fa-building stat-icon"></i>
            </div>
          </div>
          <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="stat-title">Occupied</div>
                <div class="stat-value text-success"><?= $occupied; ?></div>
              </div>
              <i class="fa-solid fa-user-check stat-icon"></i>
            </div>
          </div>
          <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="stat-title">Vacant</div>
                <div class="stat-value text-danger"><?= $vacant; ?></div>
              </div>
              <i class="fa-solid fa-door-open stat-icon"></i>
            </div>
          </div>
        </div>

        <iframe name="contentFrame" src="admin_sections/quarters.php"></iframe>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    <div>
      © <?= date("Y"); ?> Central Food Technological Research Institute (CFTRI), Mysuru — A constituent laboratory of 
      <a href="https://www.csir.res.in/" target="_blank">CSIR, Government of India</a>. 
      | Version 1.1 | Designed & Developed In-House.
    </div>
  </footer>
</body>
</html>
