<?php
session_start();
include('../db_connect.php');

// Fetch all applications
$applications = $conn->query("SELECT * FROM quarter_applications ORDER BY applied_on DESC");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $app_no = $_POST['app_no'];
    $status = $_POST['status'];
    $remarks = $_POST['remarks'];

    // Update status in applications table
    $conn->query("UPDATE quarter_applications SET status='$status' WHERE app_no='$app_no'");

    // Insert or update in application_status
    $sql = "INSERT INTO application_status (app_no, emp_no, emp_name, quarter_type, quarter_no, status, remarks)
            SELECT app_no, emp_no, emp_name, quarter_type, quarter_no, '$status', '$remarks'
            FROM quarter_applications WHERE app_no='$app_no'
            ON DUPLICATE KEY UPDATE status='$status', remarks='$remarks', updated_at=CURRENT_TIMESTAMP";

    if ($conn->query($sql)) {
        $msg = "✅ Application #$app_no updated to '$status'.";
    } else {
        $msg = "❌ Error updating application: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin | Quarter Applications</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">

<style>
body {
  background: linear-gradient(135deg, #eaf1f9, #ffffff);
  font-family: "Segoe UI", sans-serif;
  color: #0b2341;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}
.container {
  max-width: 95%;
  margin-top: 40px;
}
.header-card {
  background: #0b2341;
  color: white;
  border-radius: 12px;
  padding: 20px 30px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}
.table-container {
  margin-top: 25px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  padding: 25px;
  animation: fadeIn 1s ease-in;
}
@keyframes fadeIn {
  from {opacity: 0; transform: translateY(20px);}
  to {opacity: 1; transform: translateY(0);}
}
.table thead {
  background-color: #12294b;
  color: white;
}
.table tbody tr:hover {
  background-color: #f5f7fa;
  transform: scale(1.01);
  transition: all 0.2s ease;
}
.status-badge {
  padding: 6px 10px;
  border-radius: 6px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 5px;
}
.status-Pending { background: #ffc107; color: #000; }
.status-Accepted { background: #28a745; color: #fff; }
.status-Rejected { background: #dc3545; color: #fff; }
.search-filter {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}
.search-filter input, .search-filter select {
  border-radius: 8px;
  border: 1px solid #ccc;
  padding: 8px 12px;
  box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
}
.btn-update {
  background-color: #0b2341;
  border: none;
}
.btn-update:hover {
  background-color: #1a3a6a;
}
.no-results {
  text-align: center;
  color: #777;
  font-size: 16px;
  padding: 20px;
}
</style>
</head>

<body>
<div class="container">
  <div class="header-card d-flex justify-content-between align-items-center flex-wrap">
    <h3 class="mb-0">🏛️ Quarter Applications (Admin Review)</h3>
    <div class="search-filter mt-3 mt-md-0">
      <input type="text" id="searchInput" placeholder="🔍 Search employee or quarter...">
      <select id="statusFilter">
        <option value="">All Status</option>
        <option value="Pending">Pending</option>
        <option value="Accepted">Accepted</option>
        <option value="Rejected">Rejected</option>
      </select>
    </div>
  </div>

  <div class="table-container mt-4">
    <?php if (!empty($msg)): ?>
      <div class="alert alert-info"><?= $msg ?></div>
    <?php endif; ?>

    <table class="table table-bordered text-center align-middle" id="appTable">
      <thead>
        <tr>
          <th>App No</th>
          <th>Employee</th>
          <th>Quarter Type</th>
          <th>Quarter No</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($applications->num_rows > 0): ?>
          <?php while ($row = $applications->fetch_assoc()): ?>
            <tr class="animate__animated animate__fadeInUp">
              <td><?= $row['app_no'] ?></td>
              <td><?= htmlspecialchars($row['emp_name']) ?> (<?= htmlspecialchars($row['emp_no']) ?>)</td>
              <td><?= htmlspecialchars($row['quarter_type']) ?></td>
              <td><?= htmlspecialchars($row['quarter_no']) ?></td>
              <td><span class="status-badge status-<?= $row['status'] ?>"><?= $row['status'] ?></span></td>
              <td>
                <form method="POST" class="d-flex gap-2 justify-content-center" onsubmit="return confirmUpdate(this);">
                  <input type="hidden" name="app_no" value="<?= $row['app_no'] ?>">
                  <select name="status" class="form-select form-select-sm" required>
                    <option value="Pending" <?= $row['status']=='Pending'?'selected':'' ?>>Pending</option>
                    <option value="Accepted" <?= $row['status']=='Accepted'?'selected':'' ?>>Accept</option>
                    <option value="Rejected" <?= $row['status']=='Rejected'?'selected':'' ?>>Reject</option>
                  </select>
                  <input type="text" name="remarks" class="form-control form-control-sm" placeholder="Remarks">
                  <button type="submit" name="update_status" class="btn btn-update btn-sm text-white">Update</button>
                </form>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="6" class="no-results">No applications found</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
// Confirm before update
function confirmUpdate(form) {
  const status = form.status.value;
  return confirm(`Are you sure you want to mark this application as "${status}"?`);
}

// Search + Filter functionality
const searchInput = document.getElementById("searchInput");
const statusFilter = document.getElementById("statusFilter");

function filterTable() {
  const searchValue = searchInput.value.toLowerCase();
  const filterValue = statusFilter.value;
  const rows = document.querySelectorAll("#appTable tbody tr");

  let visible = 0;
  rows.forEach(row => {
    const text = row.innerText.toLowerCase();
    const status = row.querySelector("td:nth-child(5) span").innerText;
    const matchesSearch = text.includes(searchValue);
    const matchesFilter = !filterValue || status === filterValue;

    if (matchesSearch && matchesFilter) {
      row.style.display = "";
      visible++;
    } else {
      row.style.display = "none";
    }
  });

  const noResults = document.querySelector(".no-results");
  if (visible === 0 && !noResults) {
    const tr = document.createElement("tr");
    tr.classList.add("no-results");
    tr.innerHTML = '<td colspan="6">No matching records found</td>';
    document.querySelector("#appTable tbody").appendChild(tr);
  } else if (visible > 0 && noResults) {
    noResults.remove();
  }
}

searchInput.addEventListener("keyup", filterTable);
statusFilter.addEventListener("change", filterTable);
</script>
</body>
</html>
