<?php
include('../db_connect.php');
$result = $conn->query("SELECT * FROM occupation_vacation");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Occupation & Vacation | CFTRI Quarters</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg, #e9f1f7, #ffffff);
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
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    padding: 25px;
    animation: fadeIn 1s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.table thead {
    background-color: #12294b;
    color: white;
}

.table tbody tr:hover {
    background-color: #f1f5fa;
    transform: scale(1.01);
    transition: all 0.2s ease;
}

.search-box input {
    border-radius: 8px;
    border: 1px solid #ccc;
    padding: 10px 15px;
    width: 100%;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
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
        <h3 class="mb-0">🏢 Occupation & Vacation Records</h3>
        <div class="search-box mt-3 mt-md-0" style="width: 250px;">
            <input type="text" id="searchInput" placeholder="🔍 Search records...">
        </div>
    </div>

    <div class="table-container mt-4">
        <table class="table table-bordered table-striped align-middle text-center" id="recordsTable">
            <thead>
                <tr>
                    <th>Quarter No</th>
                    <th>Occupant Name</th>
                    <th>Employee ID</th>
                    <th>Occupied From</th>
                    <th>Vacated On</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr class="animate__animated animate__fadeInUp">
                            <td><?= htmlspecialchars($row['quarter_no']) ?></td>
                            <td><?= htmlspecialchars($row['occupant_name']) ?></td>
                            <td><?= htmlspecialchars($row['emp_id']) ?></td>
                            <td><?= htmlspecialchars($row['occupied_from']) ?></td>
                            <td><?= htmlspecialchars($row['vacated_on'] ?? '-') ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="no-results">No records found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Live Search Functionality
document.getElementById("searchInput").addEventListener("keyup", function() {
    var value = this.value.toLowerCase();
    var rows = document.querySelectorAll("#recordsTable tbody tr");
    var visible = 0;
    rows.forEach(function(row) {
        if (row.innerText.toLowerCase().includes(value)) {
            row.style.display = "";
            visible++;
        } else {
            row.style.display = "none";
        }
    });
    if (visible === 0) {
        if (!document.querySelector(".no-results")) {
            var tr = document.createElement("tr");
            tr.classList.add("no-results");
            tr.innerHTML = '<td colspan="5">No matching records found</td>';
            document.querySelector("#recordsTable tbody").appendChild(tr);
        }
    } else {
        var msg = document.querySelector(".no-results");
        if (msg) msg.remove();
    }
});
</script>
</body>
</html>
