<?php
include('../db_connect.php');
session_start();

// Temporary for testing
$_SESSION['role'] = 'admin';
$_SESSION['username'] = 'admin';

$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Fetch Type A quarters
$sql = "SELECT * FROM quarters_admin WHERE quarter_no LIKE '%A%'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Type A Quarters | CSIR–CFTRI Quarters Management</title>

<!-- Bootstrap + Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600&display=swap" rel="stylesheet">

<style>
body {
  background: #f3f6fb;
  font-family: 'Inter', sans-serif;
  color: #222;
  min-height: 100vh;
}

/* ===== NAVBAR ===== */
.navbar {
  background: linear-gradient(90deg, #002b5c, #003c80);
  box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}
.navbar-brand {
  color: #fff;
  font-weight: 600;
  font-family: 'Poppins', sans-serif;
  letter-spacing: 0.5px;
}
.navbar-brand i {
  color: #fcd703;
}
.navbar-text {
  color: #e3e8ef !important;
  font-size: 0.9rem;
}

/* ===== HEADING ===== */
h4 {
  font-weight: 600;
  color: #002b5c;
  font-family: 'Poppins', sans-serif;
}

/* ===== CARD ===== */
.card {
  border: none;
  border-radius: 12px;
  background: #fff;
  box-shadow: 0 5px 15px rgba(0,0,0,0.05);
  overflow: hidden;
}
.card-body {
  padding: 0;
}

/* ===== TABLE ===== */
.table {
  margin-bottom: 0;
}
.table thead {
  background: #002b5c;
  color: #fff;
  font-size: 0.9rem;
  text-transform: uppercase;
}
.table tbody tr:nth-child(odd) {
  background-color: #f8fafc;
}
.table-hover tbody tr:hover {
  background-color: #eaf1fb;
  transition: 0.2s ease;
}

/* ===== BADGES ===== */
.badge-occupied {
  background: #198754;
  padding: 6px 12px;
  border-radius: 8px;
}
.badge-vacant {
  background: #6c757d;
  padding: 6px 12px;
  border-radius: 8px;
}

/* ===== BUTTONS ===== */
.btn {
  border-radius: 8px;
  font-size: 0.85rem;
  padding: 6px 12px;
  transition: all 0.2s ease;
}
.btn:hover {
  transform: translateY(-1px);
}
.btn-success {
  background: linear-gradient(135deg, #198754, #28a745);
  border: none;
}
.btn-warning {
  background: linear-gradient(135deg, #ffc107, #ffca2c);
  border: none;
}
.btn-primary {
  background: linear-gradient(135deg, #003c80, #0056b3);
  border: none;
}

/* ===== MODAL ===== */
.modal-content {
  border-radius: 15px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}
.modal-header {
  background: #002b5c;
  color: #fff;
  border-top-left-radius: 15px;
  border-top-right-radius: 15px;
}
.modal-title {
  font-weight: 600;
  font-family: 'Poppins', sans-serif;
}

/* ===== FOOTER ===== */
.footer {
  text-align: center;
  padding: 20px;
  color: #777;
  font-size: 0.9rem;
}

/* ===== ANIMATIONS ===== */
@keyframes fadeInUp {
  from {opacity: 0; transform: translateY(20px);}
  to {opacity: 1; transform: translateY(0);}
}
.animate-fadeInUp {
  animation: fadeInUp 0.6s ease both;
}
</style>
</head>

<body>

<nav class="navbar navbar-expand-lg py-3">
  <div class="container">
    <a class="navbar-brand" href="#">
      <i class="bi bi-building me-2"></i> CSIR–CFTRI
    </a>
    <span class="navbar-text ms-auto">Official Quarters Management Portal</span>
  </div>
</nav>

<div class="container my-4 animate-fadeInUp">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4><i class="bi bi-houses me-2 text-primary"></i> Type A Quarters</h4>
    <?php if($isAdmin): ?>
      <button class="btn btn-success btn-sm shadow-sm" onclick="openAddModal()">
        <i class="bi bi-plus-circle"></i> Add New Quarter
      </button>
    <?php endif; ?>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <table class="table table-bordered table-hover align-middle text-center">
        <thead>
          <tr>
            <th>Quarter No</th>
            <th>RR No</th>
            <th>Employee No</th>
            <th>Occupant Name</th>
            <th>Status</th>
            <th>Updated By</th>
            <th>Last Updated</th>
            <?php if($isAdmin): ?><th>Actions</th><?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr id="row-<?= $row['quarter_no'] ?>" class="animate-fadeInUp">
              <td><strong><?= htmlspecialchars($row['quarter_no']) ?></strong></td>
              <td><?= htmlspecialchars($row['rr_no']) ?></td>
              <td><?= htmlspecialchars($row['emp_id']) ?></td>
              <td><?= htmlspecialchars($row['occupant_name']) ?></td>
              <td>
                <span class="badge <?= $row['status']=='occupied'?'badge-occupied':'badge-vacant' ?>">
                  <?= ucfirst($row['status']) ?>
                </span>
              </td>
              <td><?= htmlspecialchars($row['updated_by']) ?></td>
              <td><?= htmlspecialchars($row['updated_at']) ?></td>
              <?php if($isAdmin): ?>
              <td>
                <button class="btn btn-warning btn-sm me-1" 
                        onclick='openEditModal(<?= json_encode($row) ?>)'>
                  <i class="bi bi-pencil-square"></i>
                </button>
              </td>
              <?php endif; ?>
            </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="8" class="text-muted py-3">No Type A quarters found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal for Add/Edit -->
<div class="modal fade" id="quarterModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="quarterForm">
        <div class="modal-header">
          <h5 class="modal-title">Add / Edit Quarter</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="mode" id="formMode" value="add">

          <div class="mb-3">
            <label class="form-label">Quarter No</label>
            <input type="text" name="quarter_no" id="quarter_no" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">RR No</label>
            <input type="text" name="rr_no" id="rr_no" class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label">Employee No</label>
            <input type="text" name="emp_id" id="emp_id" class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label">Occupant Name</label>
            <input type="text" name="occupant_name" id="occupant_name" class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" id="status" class="form-select">
              <option value="occupied">Occupied</option>
              <option value="vacant">Vacant</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="footer">
  © <?= date('Y') ?> CSIR–CFTRI | Housing & Quarters Management System
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const modal = new bootstrap.Modal(document.getElementById('quarterModal'));

function openAddModal(){
  document.getElementById('quarterForm').reset();
  document.getElementById('formMode').value='add';
  document.getElementById('quarter_no').readOnly=false;
  document.querySelector('.modal-title').innerText='Add New Quarter';
  modal.show();
}

function openEditModal(data){
  document.getElementById('formMode').value='edit';
  document.getElementById('quarter_no').value=data.quarter_no;
  document.getElementById('quarter_no').readOnly=true;
  document.getElementById('rr_no').value=data.rr_no;
  document.getElementById('emp_id').value=data.emp_id;
  document.getElementById('occupant_name').value=data.occupant_name;
  document.getElementById('status').value=data.status;
  document.querySelector('.modal-title').innerText='Edit Quarter Details';
  modal.show();
}

document.getElementById('status').addEventListener('change', function() {
  if (this.value === 'vacant') {
    if (confirm("Marking as 'Vacant' will clear occupant details. Proceed?")) {
      document.getElementById('emp_id').value = '';
      document.getElementById('occupant_name').value = '';
    } else {
      this.value = 'occupied';
    }
  }
});

document.getElementById('quarterForm').addEventListener('submit', function(e){
  e.preventDefault();
  const formData = new URLSearchParams(new FormData(this));
  fetch('save_quarter.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: formData
  })
  .then(r=>r.json())
  .then(data=>{
    if(data.success){
      alert('✅ Saved successfully!');
      location.reload();
    } else {
      alert('❌ Error: '+data.message);
    }
  });
});
</script>
</body>
</html>
