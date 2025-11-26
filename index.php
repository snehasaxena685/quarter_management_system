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
            header("Location: admins_dashboard.php");
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
  <title>CFTRI Quarters Management | Login</title>
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
      align-items: flex-end;
      justify-content: flex-end;
      padding-bottom: 6%;
      background: url('assets/Mansion_pic.jpg') no-repeat center center fixed;
      background-size: cover;
      position: relative;
      overflow: hidden;
    }

    /* Make background clearer by reducing white overlay */
    body::after {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(255, 255, 255, 0.1); /* less opacity */
      backdrop-filter: blur(0.5px); /* light blur */
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
      background: rgba(255, 255, 255, 0.93);
      backdrop-filter: blur(6px);
      border-radius: 15px;
      box-shadow: 0 6px 25px rgba(0, 0, 0, 0.25);
      padding: 45px 40px;
      text-align: center;
      border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .login-card img {
      width: 85px;
      margin-bottom: 14px;
      border-radius: 50%;
      border: 2px solid #ddd;
      box-shadow: 0 0 10px rgba(0,0,0,0.15);
    }

    .login-card h2 {
      font-weight: 600;
      color: #0b2341;
      font-size: 22px;
      margin-bottom: 25px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .form-control {
      border-radius: 10px;
      border: 1px solid #cbd5e1;
      padding: 12px 14px;
      font-size: 15px;
      transition: all 0.3s ease;
      background-color: rgba(255,255,255,0.95);
    }

    .form-control:focus {
      border-color: #1e40af;
      box-shadow: 0 0 8px rgba(30, 64, 175, 0.2);
    }

    .btn-login {
      background: #1e3a8a;
      border: none;
      color: #fff;
      width: 100%;
      padding: 12px;
      border-radius: 10px;
      font-size: 16px;
      font-weight: 500;
      transition: all 0.3s ease;
      letter-spacing: 0.3px;
    }

    .btn-login:hover {
      background: #1d4ed8;
      transform: translateY(-2px);
      box-shadow: 0 4px 18px rgba(30, 58, 138, 0.25);
    }

    .error {
      margin-top: 15px;
      background: rgba(239,68,68,0.08);
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
      opacity: 0.9;
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
      .login-card {
        padding: 40px 30px;
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
