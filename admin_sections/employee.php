<?php
include('../db_connect.php');
$result = $conn->query("SELECT * FROM employee");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Employees | CFTRI Quarter Management</title>

<!-- Bootstrap & Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
body {
  background-color: #f4f6f8;
  font-family: 'Segoe UI', sans-serif;
  animation: fadeIn 0.5s ease-in;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(8px); }
  to { opacity: 1; transform: translateY(0); }
}
h4 {
  color: #0b2341;
  font-weight: 600;
}
.header-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}
.header-section .title {
  display: flex;
  align-items: center;
  gap: 10px;
}
.header-section .title i {
  color: #0b2341;
  font-size: 22px;
}
.table-container {
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.08);
  padding: 20px;
}
.table thead {
  background-color: #0b2341;
  color: #fff;
}
.table tbody tr:hover {
  background-color: #f2f5fa;
  transition: 0.3s;
}
.btn {
  border-radius: 6px;
}
.search-box {
  position: relative;
}
.search-box input {
  padding-left: 32px;
}
.search-box i {
  position: absolute;
  left: 10px;
  top: 9px;
  color: gray;
}
</style>
</head>

<body class="p-4">

<!-- Header Section -->
<div class="header-section">
  <div class="title">
    <i class="fa-solid fa-users"></i>
    <h4 class="m-0">Employee Directory</h4>
  </div>
  <div class="d-flex gap-2">
    <button class="btn btn-success btn-sm" onclick="exportTableToCSV('employees.csv')">
      <i class="fa-solid fa-file-csv"></i> Export CSV
    </button>
    <button class="btn btn-danger btn-sm" onclick="exportTableToPDF()">
      <i class="fa-solid fa-file-pdf"></i> Export PDF
    </button>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
      <i class="fa-solid fa-user-plus"></i> Add Employee
    </button>
  </div>
</div>

<!-- Search -->
<div class="search-box mb-3">
  <i class="fa-solid fa-magnifying-glass"></i>
  <input type="text" id="searchInput" class="form-control" placeholder="Search employee by name, department, or designation...">
</div>

<!-- Table -->
<div class="table-container">
  <table class="table table-bordered table-striped text-center align-middle" id="employeeTable">
    <thead>
      <tr>
        <th onclick="sortTable(0)">Emp No <i class="fa-solid fa-sort"></i></th>
        <th onclick="sortTable(1)">Employee Name <i class="fa-solid fa-sort"></i></th>
        <th onclick="sortTable(2)">Designation <i class="fa-solid fa-sort"></i></th>
        <th onclick="sortTable(3)">Department <i class="fa-solid fa-sort"></i></th>
        <th>Email</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['emp_no'] ?></td>
          <td><?= $row['employee_name'] ?></td>
          <td><?= $row['designation'] ?></td>
          <td><?= $row['department'] ?></td>
          <td><?= $row['email'] ?></td>
          <td>
            <button class="btn btn-danger btn-sm" onclick="deleteEmployee('<?= $row['emp_no'] ?>')">
              <i class="fa-solid fa-trash"></i> Delete
            </button>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<!-- Add Employee Modal -->
<div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="add_employee.php" method="POST">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="addEmployeeModalLabel"><i class="fa-solid fa-user-plus"></i> Add New Employee</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Employee No</label>
            <input type="text" name="emp_no" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Employee Name</label>
            <input type="text" name="employee_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Designation</label>
            <input type="text" name="designation" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Department</label>
            <input type="text" name="department" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control">
          </div>
          <!-- <div class="mb-3">
            <label class="form-label">Contact No</label>
            <input type="text" name="contact_no" class="form-control">
          </div>
        </div> -->
        <div class="modal-footer">
          <button type="submit" class="btn btn-success"><i class="fa-solid fa-check"></i> Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- JS: Bootstrap + Custom -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>

// Live Search
document.getElementById('searchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll("#employeeTable tbody tr");

    rows.forEach(row => {
        // Get only the text content of the first 5 columns (skip Actions column)
        let rowText = Array.from(row.querySelectorAll('td'))
                           .slice(0, 5) // first 5 columns
                           .map(td => td.textContent.toLowerCase())
                           .join(' ');

        row.style.display = rowText.includes(filter) ? '' : 'none';
    });
});


// Sort Table
function sortTable(n) {
  let table = document.getElementById("employeeTable"), switching = true, dir = "asc";
  while (switching) {
    switching = false;
    let rows = table.rows;
    for (let i = 1; i < rows.length - 1; i++) {
      let shouldSwitch = false;
      let x = rows[i].getElementsByTagName("TD")[n];
      let y = rows[i + 1].getElementsByTagName("TD")[n];
      if (dir == "asc" && x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) shouldSwitch = true;
      else if (dir == "desc" && x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) shouldSwitch = true;
      if (shouldSwitch) {
        rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
        switching = true;
        break;
      }
    }
    if (!switching && dir == "asc") { dir = "desc"; switching = true; }
  }
}

// Export CSV
function exportTableToCSV(filename) {
  let csv = [];
  const rows = document.querySelectorAll("table tr");
  rows.forEach(row => {
    let cols = row.querySelectorAll("td, th");
    let data = [];
    cols.forEach(col => data.push(col.innerText));
    csv.push(data.join(","));
  });
  const blob = new Blob([csv.join("\n")], { type: "text/csv" });
  const link = document.createElement("a");
  link.download = filename;
  link.href = URL.createObjectURL(blob);
  link.click();
}

// Export PDF
function exportTableToPDF() {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();
  doc.html(document.querySelector(".table-container"), {
    callback: function(doc) {
      doc.save("Employee_List.pdf");
    },
    x: 10,
    y: 10,
    html2canvas: { scale: 0.5 }
  });
}

// Delete Employee
function deleteEmployee(empNo) {
    if(!confirm('Are you sure you want to delete employee ' + empNo + '?')) return;

    fetch('delete_employee.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'emp_no=' + encodeURIComponent(empNo)
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            alert('Employee deleted successfully!');
            location.reload(); // Refresh table
        } else {
            alert('Error: ' + data.message);
        }
    });
}
</script>

</body>
</html>
