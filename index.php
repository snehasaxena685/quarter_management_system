<?php
session_start();
include("db_connect.php");

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'admin') {
            header("Location: aadmin_dashboard.php");
        } elseif ($user['role'] == 'staff') {
            header("Location: staff_dashboard.php");
        } else {
            header("Location: student_dashboard.php");
        }
        exit;
    } else {
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CFTRI Quarter Management | Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      height: 100vh;
      font-family: 'Poppins', sans-serif;
      display: flex;
      align-items: flex-end; /* shifts box lower */
      justify-content: flex-end;
      padding-bottom: 6%; /* controls vertical position */
      background: url('assets/1.jpg') no-repeat center center fixed;
      background-size: cover;
      position: relative;
      overflow: hidden;
    }

    body::after {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(1.5px);
      z-index: 0;
    }

    .login-container {
      position: relative;
      z-index: 1;
      margin-right: 6%;
      width: 380px;
      animation: fadeIn 1s ease-in-out;
    }

    .login-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 18px;
      box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
      padding: 45px 40px;
      text-align: center;
      border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .login-card img {
      width: 85px;
      margin-bottom: 12px;
    }

    .login-card h2 {
      font-weight: 600;
      color: #0b2341;
      font-size: 22px;
      margin-bottom: 25px;
    }

    .form-control {
      border-radius: 10px;
      border: 1px solid #cbd5e1;
      padding: 12px 14px;
      font-size: 15px;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: #2563eb;
      box-shadow: 0 0 10px rgba(37, 99, 235, 0.25);
    }

    .btn-login {
      background: linear-gradient(135deg, #1e40af, #2563eb);
      border: none;
      color: #fff;
      width: 100%;
      padding: 12px;
      border-radius: 10px;
      font-size: 16px;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .btn-login:hover {
      background: linear-gradient(135deg, #1d4ed8, #1e3a8a);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(37, 99, 235, 0.25);
    }

    .error {
      margin-top: 15px;
      background: rgba(239,68,68,0.1);
      border-left: 4px solid #ef4444;
      padding: 10px 12px;
      border-radius: 6px;
      color: #b91c1c;
      font-size: 14px;
      animation: shake 0.4s ease;
    }

    .footer-text {
      font-size: 13px;
      color: #6b7280;
      margin-top: 25px;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(25px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-4px); }
      50% { transform: translateX(4px); }
      75% { transform: translateX(-4px); }
    }

    @media (max-width: 768px) {
      body {
        align-items: center;
        justify-content: center;
        padding-bottom: 0;
      }
      .login-container {
        margin-right: 0;
        width: 90%;
      }
    }
  </style>
</head>
<body>

  <div class="login-container">
    <div class="login-card">
      <img src="assets/cftri_logo.jpg" alt="CFTRI Logo">
      <h2>CFTRI Quarter Management</h2>

      <form method="POST" action="">
        <input type="text" name="username" class="form-control mb-3" placeholder="Enter Username" required>
        <input type="password" name="password" class="form-control mb-3" placeholder="Enter Password" required>
        <button type="submit" class="btn-login">Login</button>
      </form>

      <?php if($error): ?>
        <div class="error mt-3"><?= $error ?></div>
      <?php endif; ?>

      <div class="footer-text">
        © 2025 CSIR-CFTRI | Quarter Management System
      </div>
    </div>
  </div>

</body>
</html>
