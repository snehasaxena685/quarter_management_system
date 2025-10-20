<?php
include('../db_connect.php'); // adjust path if needed

// Fetch only quarters where 'quarter_no' contains 'C' (case-insensitive)
$sql = "SELECT * FROM quarters WHERE quarter_no LIKE '%C%'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Type C Quarters | CFTRI Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background-color: #f4f6f8;
    font-family: 'Segoe UI', sans-serif;
}
h4 {
    color: #0b2341;
    font-weight: 600;
}
.table {
    background: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    border-radius: 6px;
    overflow: hidden;
}
.table thead {
    background-color: #12294b;
    color: white;
}
.table tbody tr:hover {
    background-color: #f1f3f6;
}
.back-btn {
    text-decoration: none;
    color: #0b2341;
    font-weight: 500;
}
.back-btn:hover {
    text-decoration: underline;
}
</style>
</head>

<body class="p-4">

<a href="quarters.php" class="back-btn">&larr; Back to Categories</a>

<h4 class="mt-3">Type C Quarters</h4>

<table class="table table-bordered table-striped mt-3">
  <thead class="table-dark">
    <tr>
      <th>ID</th>
      <th>Quarter No</th>
      <th>RR No</th>
      <th>Quarter Size</th>
      <th>Garage</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['id']) ?></td>
        <td><?= htmlspecialchars($row['quarter_no']) ?></td>
        <td><?= htmlspecialchars($row['rr_no']) ?></td>
        <td><?= htmlspecialchars($row['quarter_size']) ?></td>
        <td><?= htmlspecialchars($row['garage']) ?></td>
      </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="5" class="text-center text-muted">No Type C quarters found.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

</body>
</html>
