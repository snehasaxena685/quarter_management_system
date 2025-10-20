<?php
include('../db_connect.php'); // Database connection

// Get application number from GET request
$app_no = $_GET['app_no'] ?? '';

// Array to store application data
$applications = [];

if ($app_no) {
    $stmt = $conn->prepare("SELECT * FROM application_status WHERE app_no=? ORDER BY updated_at DESC");
    $stmt->bind_param("s", $app_no);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $applications[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Check Application Status</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background:#f8f9fa; font-family:'Segoe UI',sans-serif; }
.status-badge { padding:0.3rem 0.6rem; border-radius:0.35rem; font-weight:500; color:#fff; }
.status-Pending { background-color:#ffc107; color:#000; }
.status-Accepted { background-color:#28a745; }
.status-Rejected { background-color:#dc3545; }
</style>
</head>
<body class="p-4">
<div class="container">
    <h3 class="text-primary mb-3">Check Application Status</h3>

    <!-- Form to enter application number -->
    <form method="GET" class="mb-4 row g-2">
        <div class="col-md-4">
            <input type="text" name="app_no" class="form-control" placeholder="Enter Application Number" required>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Check Status</button>
        </div>
    </form>

    <?php if($app_no): ?>
        <?php if(!empty($applications)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Application No</th>
                            <th>Employee No</th>
                            <th>Employee Name</th>
                            <th>Quarter Type</th>
                            <th>Quarter No</th>
                            <th>Status</th>
                            <th>Remarks</th>
                            <th>Last Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($applications as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['app_no']) ?></td>
                            <td><?= htmlspecialchars($row['emp_no']) ?></td>
                            <td><?= htmlspecialchars($row['emp_name']) ?></td>
                            <td><?= htmlspecialchars($row['quarter_type']) ?></td>
                            <td><?= htmlspecialchars($row['quarter_no']) ?></td>
                            <td>
                                <span class="status-badge status-<?= $row['status'] ?? 'Pending' ?>">
                                    <?= $row['status'] ?? 'Pending' ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($row['remarks'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['updated_at'] ?? '-') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">❌ No application found with this number.</div>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>
