<?php
session_start();
include("../db_connect.php");

// Restrict to admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Handle messages
$message = "";

// --- ADD USER ---
if (isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $password = md5(trim($_POST['password']));
    $role = $_POST['role'];

    $check = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        $message = "<div class='alert alert-warning'>User already exists!</div>";
    } else {
        $insert = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $insert->bind_param("sss", $username, $password, $role);
        if ($insert->execute()) {
            $message = "<div class='alert alert-success'>User created successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error creating user!</div>";
        }
    }
}

// --- DELETE USER ---
if (isset($_GET['delete'])) {
    $uid = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE id=$uid");
    $message = "<div class='alert alert-danger'>User deleted successfully!</div>";
}

// --- FETCH USERS ---
$users = $conn->query("SELECT * FROM users ORDER BY role ASC, username ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Users | Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
body {
    background-color: #f8f9fa;
    font-family: 'Segoe UI', sans-serif;
}
.container {
    margin-top: 50px;
}
.card {
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
.btn {
    border-radius: 20px;
}
.table th {
    background-color: #0b2341;
    color: white;
}
h2 {
    color: #0b2341;
    font-weight: 600;
}
</style>
</head>
<body>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fa-solid fa-user-gear me-2"></i>Manage Users</h2>
        <a href="../admin_dashboard.php" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <?= $message ?>

    <!-- Add New User -->
    <div class="card mb-4 p-4">
        <h5 class="mb-3">Add New User</h5>
        <form method="POST" class="row g-3">
            <div class="col-md-4">
                <label>Username (Emp No)</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label>Role</label>
                <select name="role" class="form-select" required>
                    <option value="staff">Staff</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" name="add_user" class="btn btn-primary w-100">
                    <i class="fa-solid fa-user-plus"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="card p-4">
        <h5 class="mb-3">Existing Users</h5>
        <table class="table table-bordered table-hover align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username (Emp No)</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($users->num_rows > 0): ?>
                    <?php while($row = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td>
                            <?php if($row['role'] == 'admin'): ?>
                                <span class="badge bg-danger">Admin</span>
                            <?php else: ?>
                                <span class="badge bg-primary">Staff</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" 
                               onclick="return confirm('Are you sure you want to delete this user?')">
                               <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center text-muted">No users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
