<?php
include('../db_connect.php');

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_no = $_POST['emp_no'];
    $emp_name = $_POST['emp_name'];
    $quarter_type = $_POST['quarter_type'];
    $quarter_no = $_POST['quarter_no'];
    $email = $_POST['email'];

    $sql = "INSERT INTO quarter_applications (emp_no, emp_name, quarter_type, quarter_no, email)
            VALUES ('$emp_no', '$emp_name', '$quarter_type', '$quarter_no', '$email')";
    
    if ($conn->query($sql)) {
        // Fetch generated app_no
        $app = $conn->query("SELECT app_no FROM quarter_applications ORDER BY id DESC LIMIT 1")->fetch_assoc();
        $app_no = $app['app_no'];
        $msg = "✅ Thank you $emp_name! Your application has been submitted successfully. 
                Your Application Number is <b>$app_no</b>. Please save it for future tracking.";
    } else {
        $msg = "❌ Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Apply for Quarters</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background:#f8f9fa; font-family:'Segoe UI',sans-serif; }
.card { max-width:600px; margin:auto; border-radius:12px; }
h4 { color:#0b2341; }
</style>
</head>
<body class="p-4">
<div class="container">
  <div class="card shadow p-4">
    <h4 class="mb-3 text-center">Apply for Quarters</h4>

    <?php if ($msg): ?>
      <div class="alert alert-info"><?= $msg ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Employee No</label>
        <input type="text" name="emp_no" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Employee Name</label>
        <input type="text" name="emp_name" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Quarter Type</label>
        <select name="quarter_type" class="form-select" required>
          <option value="">Select Type</option>
          <option value="A">Type A</option>
          <option value="B">Type B</option>
          <option value="C">Type C</option>
          <option value="D">Type D</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Quarter No</label>
        <input type="text" name="quarter_no" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email (optional)</label>
        <input type="email" name="email" class="form-control">
      </div>
      <button type="submit" class="btn btn-primary w-100">Submit Application</button>
    </form>
  </div>
</div>
</body>
</html>
