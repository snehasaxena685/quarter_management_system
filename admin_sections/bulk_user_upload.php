<?php
// admin_sections/bulk_user_upload.php
session_start();
require_once "../db_connect.php"; // adjust path if needed

// Restrict to admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$report = [
    'total' => 0,
    'created' => 0,
    'updated' => 0,
    'failed' => 0,
    'errors' => []
];

function normalize_designation($designation) {
    // Remove non-alphanumeric, trim, uppercase. "PA II" -> "PAII"
    $d = preg_replace('/[^A-Za-z0-9]/', '', $designation);
    return strtoupper($d);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $report['errors'][] = "Upload error code: " . $file['error'];
    } else {
        $tmpPath = $file['tmp_name'];

        // Basic MIME check (not bulletproof)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $tmpPath);
        finfo_close($finfo);

        // Accept common CSV types
        $acceptedMimes = ['text/plain','text/csv','application/vnd.ms-excel','text/comma-separated-values','application/csv','text/x-csv','application/vnd.msexcel'];
        if (!in_array($mime, $acceptedMimes)) {
            // still allow — some servers give different MIME, only warn
            // $report['errors'][] = "Uploaded file MIME type: $mime (expected CSV). Proceeding anyway.";
        }

        if (($handle = fopen($tmpPath, 'r')) !== false) {
            // Read header
            $header = fgetcsv($handle);
            if (!$header) {
                $report['errors'][] = "CSV appears empty or malformed.";
                fclose($handle);
            } else {
                // Normalize header columns to lowercase trimmed keys
                $cols = array_map(function($c){ return strtolower(trim($c)); }, $header);

                // Required: 'emp_no' and 'designation' must be present
                if (!in_array('emp_no', $cols) || !in_array('designation', $cols)) {
                    $report['errors'][] = "CSV must include at least 'emp_no' and 'designation' columns.";
                    fclose($handle);
                } else {
                    // Map column name to index
                    $colIndex = [];
                    foreach ($cols as $i => $c) $colIndex[$c] = $i;

                    // Begin transaction
                    $conn->begin_transaction();
                    $lineNo = 1;
                    while (($row = fgetcsv($handle)) !== false) {
                        $lineNo++;
                        $report['total']++;

                        // Get emp_no and designation safely
                        $emp_no = isset($row[$colIndex['emp_no']]) ? trim($row[$colIndex['emp_no']]) : '';
                        $designation = isset($row[$colIndex['designation']]) ? trim($row[$colIndex['designation']]) : '';
                        $occupant_name = isset($colIndex['occupant_name']) ? trim($row[$colIndex['occupant_name']]) : null;

                        if ($emp_no === '' || $designation === '') {
                            $report['failed']++;
                            $report['errors'][] = "Line $lineNo: emp_no or designation empty — skipped.";
                            continue;
                        }

                        // Build plain password (emp_no + normalized designation)
                        $normDesignation = normalize_designation($designation);
                        $plainPassword = $emp_no . $normDesignation;

                        // Hash password securely
                        $passwordHash = password_hash($plainPassword, PASSWORD_DEFAULT);

                        // Insert or update user
                        // We'll use INSERT ... ON DUPLICATE KEY UPDATE to upsert
                        $stmt = $conn->prepare("
                            INSERT INTO users (username, password, role, created_at, updated_at)
                            VALUES (?, ?, 'staff', NOW(), NULL)
                            ON DUPLICATE KEY UPDATE password = VALUES(password), role = 'staff', updated_at = NOW()
                        ");
                        if ($stmt === false) {
                            $report['failed']++;
                            $report['errors'][] = "Line $lineNo: Prepare failed - " . $conn->error;
                            continue;
                        }
                        $stmt->bind_param('ss', $emp_no, $passwordHash);
                        if ($stmt->execute()) {
                            // If a row existed, affected_rows == 2 (insert replaced?) — safer to detect via SQL warning?
                            // We will check if this was an insert or update using affected_rows:
                            if ($stmt->affected_rows > 0) {
                                // MySQL returns affected_rows = 1 for insert, 2 for update when using ON DUPLICATE KEY
                                // But this depends. To be simple, check if user existed before by running a quick SELECT:
                                $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
                                $check->bind_param("s", $emp_no);
                                $check->execute();
                                $res = $check->get_result();
                                if ($res && $res->num_rows === 1) {
                                    // If created_at equals updated_at? Hard to check; we'll increment created for first insert using lastInsertId
                                    if ($stmt->insert_id) {
                                        $report['created']++;
                                    } else {
                                        $report['updated']++;
                                    }
                                } else {
                                    $report['created']++;
                                }
                                $check->close();
                            } else {
                                // technically no rows affected — still consider updated
                                $report['updated']++;
                            }
                        } else {
                            $report['failed']++;
                            $report['errors'][] = "Line $lineNo: Failed to insert/update user ({$emp_no}) - " . $stmt->error;
                        }
                        $stmt->close();
                    } // end while rows

                    // commit
                    $conn->commit();
                    fclose($handle);
                }
            }
        } else {
            $report['errors'][] = "Unable to open uploaded file.";
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Bulk Upload Users — Manage Users</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background:#f4f6f8; font-family: 'Segoe UI', sans-serif; padding:20px; }
.container { max-width:900px; margin:0 auto; }
.card { border-radius:10px; box-shadow:0 3px 10px rgba(0,0,0,0.08); }
pre { white-space:pre-wrap; }
.small-muted { color:#6c757d; font-size:0.9rem; }
</style>
</head>
<body>
<div class="container">
  <div class="card p-4 mb-3">
    <h4>Bulk Upload Users from CSV</h4>
    <p class="small-muted">Upload a CSV with columns including <code>emp_no</code> and <code>designation</code>. The script will create or update users with username=emp_no and password = emp_no + normalized(designation). Passwords are stored hashed.</p>

    <form method="post" enctype="multipart/form-data" class="row g-3 align-items-end">
      <div class="col-md-8">
        <label class="form-label">CSV File</label>
        <input type="file" name="csv_file" accept=".csv,text/csv" class="form-control" required>
      </div>
      <div class="col-md-4">
        <button type="submit" class="btn btn-primary w-100">Upload & Create Users</button>
      </div>
    </form>
  </div>

  <?php if ($report['total'] > 0): ?>
  <div class="card p-3 mb-3">
    <h5>Upload Summary</h5>
    <ul>
      <li>Total rows processed: <strong><?= $report['total'] ?></strong></li>
      <li>Created: <strong><?= $report['created'] ?></strong></li>
      <li>Updated: <strong><?= $report['updated'] ?></strong></li>
      <li>Failed: <strong><?= $report['failed'] ?></strong></li>
    </ul>
    <?php if (!empty($report['errors'])): ?>
      <h6>Errors / Warnings</h6>
      <pre><?= htmlspecialchars(implode("\n", $report['errors'])) ?></pre>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <div class="card p-3">
    <a href="manage_users.php" class="btn btn-outline-secondary mb-2">Back to Manage Users</a>
    <a href="../admin_dashboard.php" class="btn btn-outline-secondary mb-2 ms-2">Admin Dashboard</a>
  </div>
</div>
</body>
</html>
