<?php
include('../db_connect.php');
$result = $conn->query("SELECT * FROM quarters");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Quarters</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>



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
</style>





<body class="p-3">
<h4> Quarter Details</h4>
<table class="table table-bordered table-striped mt-3">
  <thead class="table-dark">
    <tr>
      <th>ID</th>
      <th>Quarter No</th>
      <th>RR No</th>
      <th>garage</th>
      <th>Quarter Size</th>
      <!-- <th>Vacated Date</th> -->
    </tr>
  </thead>
  <tbody>
    <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['quarter_no'] ?></td>
        <td><?= $row['rr_no'] ?></td>
        <td><?= $row['garage'] ?></td>
        <td><?= $row['quarter_size'] ?></td>
        <!-- <td><?= $row['vacated_date'] ?></td> -->
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>
</body>
</html>
