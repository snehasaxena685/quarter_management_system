<?php
include('./db_connect.php');

// Fetch all quarters
$query = "SELECT quarter_no, rr_no, status FROM quarters_admin ORDER BY quarter_no ASC";
$result = $conn->query($query);

// Group and count by type
$quarters_by_type = [
  'A' => [], 'B' => [], 'C' => [], 'D' => [], 'E' => [], 'FA' => [], 'Other' => []
];
$summary = [
  'total' => 0,
  'occupied' => 0,
  'vacant' => 0,
  'types' => [
    'A' => ['occupied' => 0, 'vacant' => 0],
    'B' => ['occupied' => 0, 'vacant' => 0],
    'C' => ['occupied' => 0, 'vacant' => 0],
    'D' => ['occupied' => 0, 'vacant' => 0],
    'E' => ['occupied' => 0, 'vacant' => 0],
    'FA' => ['occupied' => 0, 'vacant' => 0],
    'Other' => ['occupied' => 0, 'vacant' => 0],
  ]
];

while ($row = $result->fetch_assoc()) {
    $q_no = strtoupper($row['quarter_no']);
    $status = strtolower($row['status']);
    $summary['total']++;

    if ($status === 'occupied') $summary['occupied']++;
    else $summary['vacant']++;

    $type = 'Other';
    if (strpos($q_no, 'A-') === 0) $type = 'A';
    elseif (strpos($q_no, 'B-') === 0) $type = 'B';
    elseif (strpos($q_no, 'C-') === 0) $type = 'C';
    elseif (strpos($q_no, 'D-') === 0) $type = 'D';
    elseif (strpos($q_no, 'E-') === 0) $type = 'E';
    elseif (strpos($q_no, 'FA-') === 0) $type = 'FA';

    $quarters_by_type[$type][] = $row;
    if ($status === 'occupied') $summary['types'][$type]['occupied']++;
    else $summary['types'][$type]['vacant']++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>CFTRI Quarters Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<style>
body { background: #f4f6f8; font-family: 'Segoe UI', sans-serif; }
h4 { color: #0b2341; font-weight: 600; }
.card-summary {
  background: white; border-radius: 10px; padding: 15px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center;
}
.card-summary h5 { margin: 0; font-size: 18px; color: #0b2341; }
.card-summary p { font-size: 14px; margin: 4px 0; }
.status-occupied { color: #d9534f; font-weight: 600; }
.status-vacant { color: #28a745; font-weight: 600; }
.accordion-button { background: #0b2341 !important; color: white !important; font-weight: 500; }
.accordion-button:not(.collapsed) { background: #102c57 !important; }
.btn-report { background-color: #0b2341; color: #fff; font-weight: 500; border-radius: 6px; }
.btn-report:hover { background-color: #0d3a6a; color: #fff; }
</style>
</head>
<body class="p-4">

<div class="container">
  <h4 class="mb-4">🏠 CFTRI Quarters - Real-Time Occupancy Dashboard</h4>

  <!-- Summary Section -->
  <div class="row mb-4">
    <div class="col-md-3"><div class="card-summary">
      <h5>Total Quarters</h5><p><strong><?= $summary['total'] ?></strong></p>
    </div></div>
    <div class="col-md-3"><div class="card-summary">
      <h5>Occupied</h5><p class="status-occupied"><?= $summary['occupied'] ?></p>
    </div></div>
    <div class="col-md-3"><div class="card-summary">
      <h5>Vacant</h5><p class="status-vacant"><?= $summary['vacant'] ?></p>
    </div></div>
    <div class="col-md-3 text-end">
      <button class="btn btn-success btn-sm" onclick="downloadCSV()">CSV</button>
      <button class="btn btn-danger btn-sm" onclick="downloadPDF()">PDF</button>
      <button class="btn btn-report btn-sm" onclick="downloadVacantReport()">Vacant Report PDF</button>
    </div>
  </div>

  <!-- Type-wise Summary -->
  <div class="row mb-4">
    <?php foreach ($summary['types'] as $type => $counts): if ($counts['occupied'] + $counts['vacant'] > 0): ?>
      <div class="col-md-2 col-6 mb-2">
        <div class="card-summary">
          <h5><?= $type ?> Type</h5>
          <p class="status-occupied">Occupied: <?= $counts['occupied'] ?></p>
          <p class="status-vacant">Vacant: <?= $counts['vacant'] ?></p>
        </div>
      </div>
    <?php endif; endforeach; ?>
  </div>

  <!-- Search & Filter -->
  <div class="row mb-3">
    <div class="col-md-6">
      <input type="text" id="searchInput" class="form-control" placeholder="🔍 Search by Quarter or RR No...">
    </div>
    <div class="col-md-6 text-end">
      <select id="typeSelect" class="form-select d-inline-block w-auto">
        <option value="all">All Types</option>
        <option value="A">A Type</option><option value="B">B Type</option>
        <option value="C">C Type</option><option value="D">D Type</option>
        <option value="E">E Type</option><option value="FA">FA Type</option>
      </select>
    </div>
  </div>

  <!-- Accordion by Type -->
  <div class="accordion" id="quartersAccordion">
    <?php $index = 0; foreach ($quarters_by_type as $type => $quarters): ?>
      <?php if (!empty($quarters)): $index++; ?>
        <div class="accordion-item mb-2 quarter-type" data-type="<?= $type ?>">
          <h2 class="accordion-header" id="heading<?= $index ?>">
            <button class="accordion-button <?= $index === 1 ? '' : 'collapsed' ?>" type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapse<?= $index ?>">
              <?= $type ?> Type Quarters (<?= count($quarters) ?>)
            </button>
          </h2>
          <div id="collapse<?= $index ?>" class="accordion-collapse collapse <?= $index === 1 ? 'show' : '' ?>">
            <div class="accordion-body">
              <table class="table table-bordered text-center align-middle">
                <thead>
                  <tr><th>Quarter No</th><th>RR No</th><th>Status</th></tr>
                </thead>
                <tbody>
                  <?php foreach ($quarters as $row): ?>
                    <tr>
                      <td><?= htmlspecialchars($row['quarter_no']) ?></td>
                      <td><?= htmlspecialchars($row['rr_no'] ?? '-') ?></td>
                      <td class="<?= $row['status'] == 'occupied' ? 'status-occupied' : 'status-vacant' ?>">
                        <?= ucfirst($row['status']) ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/a2d9d5a64c.js" crossorigin="anonymous"></script>

<script>
// Live Search
document.getElementById('searchInput').addEventListener('keyup', function() {
  const filter = this.value.toLowerCase();
  document.querySelectorAll("tbody tr").forEach(row => {
    row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
  });
});

// Filter by Type
document.getElementById('typeSelect').addEventListener('change', function() {
  const selected = this.value;
  document.querySelectorAll('.quarter-type').forEach(sec => {
    sec.style.display = (selected === 'all' || sec.dataset.type === selected) ? '' : 'none';
  });
});

// CSV Export
function downloadCSV() {
  const type = document.getElementById('typeSelect').value;
  let csv = "Quarter No,RR No,Status\n";
  const visibleSections = type === 'all'
    ? document.querySelectorAll('tbody tr')
    : document.querySelectorAll(`.quarter-type[data-type="${type}"] tbody tr`);
  visibleSections.forEach(row => {
    const cols = row.querySelectorAll('td');
    const rowData = Array.from(cols).map(td => td.innerText).join(',');
    csv += rowData + "\n";
  });
  const blob = new Blob([csv], { type: 'text/csv' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = `Quarters_${type === 'all' ? 'All_Types' : type + '_Type'}.csv`;
  link.click();
}

// 🧾 Customized Vacant PDF Report
function downloadVacantReport() {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF({ orientation: "portrait", unit: "pt", format: "a4" });

  // Header
  doc.setFontSize(16);
  doc.text("Council of Scientific & Industrial Research (CFTRI)", 70, 40);
  doc.setFontSize(13);
  doc.text("Vacant Quarters Report", 200, 65);
  doc.setFontSize(10);
  doc.text("Generated on: " + new Date().toLocaleString(), 400, 80);

  // Table headers
  doc.setFontSize(11);
  doc.text("Quarter No", 60, 110);
  doc.text("RR No", 200, 110);
  doc.text("Status", 320, 110);

  let y = 130;
  document.querySelectorAll("tbody tr").forEach(row => {
    const cols = row.querySelectorAll("td");
    if (cols[2] && cols[2].innerText.toLowerCase().includes("vacant")) {
      doc.text(cols[0].innerText, 60, y);
      doc.text(cols[1].innerText || "-", 200, y);
      doc.text("Vacant", 320, y);
      y += 20;
      if (y > 750) { // new page
        doc.addPage();
        y = 60;
      }
    }
  });

  // Footer
  doc.setFontSize(9);
  doc.text("This is a system-generated report from CFTRI Quarters Management Portal.", 60, 800);

  doc.save("CFTRI_Vacant_Quarters_Report.pdf");
}

// PDF Export (general)
function downloadPDF() {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF({ orientation: "landscape", unit: "pt", format: "a4" });
  const type = document.getElementById('typeSelect').value;
  const title = `CFTRI Quarters Report - ${type === 'all' ? 'All Types' : type + ' Type'}`;

  const element = (type === 'all')
    ? document.querySelector('.accordion')
    : document.querySelector(`.quarter-type[data-type="${type}"] table`);

  html2canvas(element, { scale: 1 }).then(canvas => {
    const imgData = canvas.toDataURL('image/png');
    const pageWidth = doc.internal.pageSize.getWidth();
    const imgWidth = pageWidth - 40;
    const imgHeight = canvas.height * imgWidth / canvas.width;

    doc.setFontSize(14);
    doc.text(title, 30, 30);
    doc.addImage(imgData, 'PNG', 20, 50, imgWidth, imgHeight);
    doc.save(`${title.replace(/\s+/g, '_')}.pdf`);
  });
}
</script>

</body>
</html>