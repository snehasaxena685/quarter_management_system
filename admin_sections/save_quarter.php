<?php
include('../db_connect.php');
session_start();

$response = ['success' => false, 'message' => 'Unknown error'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mode = $_POST['mode'];
    $quarter_no = $_POST['quarter_no'];
    $rr_no = $_POST['rr_no'] ?? '';
    $emp_id = $_POST['emp_id'] ?? '';
    $occupant_name = $_POST['occupant_name'] ?? '';
    $status = $_POST['status'] ?? 'vacant';
    $updated_by = $_SESSION['username'] ?? 'admin';

    // If status = vacant, clear occupant details
    if ($status == 'vacant') {
        $emp_id = '';
        $occupant_name = '';
    }

    // 🔹 Insert or update quarters_admin
    if ($mode == 'add') {
        $sql = "INSERT INTO quarters_admin (quarter_no, rr_no, emp_id, occupant_name, status, updated_by)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $quarter_no, $rr_no, $emp_id, $occupant_name, $status, $updated_by);
    } else {
        $sql = "UPDATE quarters_admin 
                SET rr_no=?, emp_id=?, occupant_name=?, status=?, updated_by=?, updated_at=NOW()
                WHERE quarter_no=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $rr_no, $emp_id, $occupant_name, $status, $updated_by, $quarter_no);
    }

    if ($stmt->execute()) {
        // ✅ Sync with occupation_vacation
        if ($status == 'occupied') {
            // If occupied — insert or update occupation record
            $check = $conn->prepare("SELECT id FROM occupation_vacation WHERE quarter_no=? AND vacated_on IS NULL");
            $check->bind_param("s", $quarter_no);
            $check->execute();
            $result = $check->get_result();

            if ($result->num_rows == 0) {
                $insert = $conn->prepare("INSERT INTO occupation_vacation (quarter_no, occupant_name, occupied_from)
                                          VALUES (?, ?, CURDATE())");
                $insert->bind_param("ss", $quarter_no, $occupant_name);
                $insert->execute();
                $insert->close();
            }
            $check->close();

        } elseif ($status == 'vacant') {
            // If vacant — mark vacated_on for last active record
            $update = $conn->prepare("UPDATE occupation_vacation 
                                      SET vacated_on = CURDATE() 
                                      WHERE quarter_no=? AND vacated_on IS NULL");
            $update->bind_param("s", $quarter_no);
            $update->execute();
            $update->close();
        }

        $response = ['success' => true];
    } else {
        $response = ['success' => false, 'message' => $conn->error];
    }

    $stmt->close();
    $conn->close();
}

echo json_encode($response);
?>
