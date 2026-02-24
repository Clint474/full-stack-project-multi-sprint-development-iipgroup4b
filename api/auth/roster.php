<?php
session_start();

function requireManager() {
    if (!isset($_SESSION['userTypeNr']) || $_SESSION['userTypeNr'] != 2) {
        header("Location: ../unauthorised.php");
        exit();
    }
}
?>
