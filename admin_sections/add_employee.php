<?php
include('../db_connect.php');

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_no = mysqli_real_escape_string($conn, $_POST['emp_no']);
    $employee_name = mysqli_real_escape_string($conn, $_POST['employee_name']);
    $designation = mysqli_real_escape_string($conn, $_POST['designation']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $email = mysqli_real_escape_string($conn, $_POST['email']); // Added email field if you have it

    // Insert query without contact_no
    $query = "INSERT INTO employee (emp_no, employee_name, designation, department, email)
              VALUES ('$emp_no', '$employee_name', '$designation', '$department', '$email')";

    if ($conn->query($query)) {
        echo "<script>
                alert('✅ Employee added successfully!');
                window.location.href = 'employee.php';
              </script>";
    } else {
        echo "<script>
                alert('❌ Error adding employee: " . $conn->error . "');
                window.location.href = 'employee.php';
              </script>";
    }
}
?>
