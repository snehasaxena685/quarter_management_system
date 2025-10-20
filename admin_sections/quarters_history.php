<?php
include('../db_connect.php');

$quarter_no = $_GET['quarter_no'] ?? '';

if($quarter_no){
    $stmt = $conn->prepare("SELECT * FROM quarters_history WHERE quarter_no=? ORDER BY changed_at DESC");
    $stmt->bind_param("s", $quarter_no);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Quarter History</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #f4f6f8; font-family: 'Segoe UI', sans-serif; }
.table { background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.05); border-radius: 6px; }
.table thead { background-color: #12294b; color: white; }
.table tbody tr:hover { background-color: #f1f3f6; }
</style>
</head>
<body class="p-3">
<h4>Quarter History</h4>

<form method="GET" class="mb-3">
    <div class="input-group" style="max-width:400px;">
        <input type="text" name="quarter_no" class="form-control" placeholder="Enter Quarter No (e.g. A-8)" value="<?= htmlspecialchars($quarter_no) ?>">
        <button class="btn btn-primary" type="submit">Search</button>
    </div>
</form>

<?php if($quarter_no && $result->num_rows > 0): ?>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Date & Time</th>
            <th>Status</th>
            <th>Occupant Name</th>
            <th>Employee No</th>
            <th>Changed By</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['changed_at'] ?></td>
            <td><?= ucfirst($row['status']) ?></td>
            <td><?= htmlspecialchars($row['occupant_name']) ?></td>
            <td><?= htmlspecialchars($row['emp_id']) ?></td>
            <td><?= htmlspecialchars($row['changed_by']) ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php elseif($quarter_no): ?>
<p class="text-muted">No history found for this quarter.</p>
<?php endif; ?>

</body>
</html>
