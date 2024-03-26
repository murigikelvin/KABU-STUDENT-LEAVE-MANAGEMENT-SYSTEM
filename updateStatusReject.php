<?php

require_once("DBConnection.php");
session_start();

if (!isset($_SESSION["sess_user"])) {
    header("Location: index.php");
} else {
    $eid = $_GET['eid'];
    $descr = $_GET['descr'];

    if (isset($_POST['action']) && $_POST['action'] == 'reject') {
        $eid = $_POST['eid'];
        $remarks = $_POST['remarks'];
        
        // Check if the current status is not 'Rejected' before updating
        $check_status_query = "SELECT status FROM leaves WHERE eid='$eid' AND descr='$descr'";
        $status_result = mysqli_query($conn, $check_status_query);
        $row = mysqli_fetch_assoc($status_result);
        $current_status = $row['status'];
        
        if ($current_status !== 'Rejected') {
            $add_to_db = mysqli_query($conn, "UPDATE leaves SET status='Rejected', remarks='$remarks' WHERE eid='$eid'");

            if ($add_to_db) {
                echo "Saved!!";
                header("Location: admin.php");
            } else {
                echo "Query Error : " . "UPDATE leaves SET status='Rejected', remarks='$remarks' WHERE eid='$eid'" . "<br>" . mysqli_error($conn);
            }
        } else {
            echo "Cannot update. The status is already 'Rejected'.";
        }
    }
}

ini_set('display_errors', true);
error_reporting(E_ALL);
?>
