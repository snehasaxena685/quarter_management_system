<?php
session_start();
include('db_connect.php'); // adjust path

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

$username = trim($_POST['username']);
$password = trim($_POST['password']);

// If stored hashed with MD5 (legacy), compute MD5; if password_hash was used, use password_verify
$hashed = md5($password); // <-- if your DB uses md5 hashes

// Prepare + execute
$stmt = $conn->prepare("
    SELECT users.username, users.role,
           employees.emp_no, employees.occupant_name, employees.designation,
           employees.department, employees.level
    FROM users
    LEFT JOIN employees ON users.username = employees.emp_no
    WHERE users.username = ? AND users.password = ?
    LIMIT 1
");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("ss", $username, $hashed);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows === 1) {
    $row = $res->fetch_assoc();
    // set session
    $_SESSION['username'] = $row['username'];
    $_SESSION['role'] = $row['role'];

    if ($row['role'] === 'staff') {
        $_SESSION['emp_no']      = $row['emp_no'];
        $_SESSION['name']        = $row['occupant_name'];
        $_SESSION['designation'] = $row['designation'];
        $_SESSION['department']  = $row['department'];
        $_SESSION['level']       = $row['level'];
        header("Location: staff_sections/staff_dashboard.php");
    } else {
        header("Location: admin_sections/admin_dashboard.php");
    }
    exit();
} else {
    echo "<script>alert('Invalid credentials'); window.location='login.php';</script>";
}
