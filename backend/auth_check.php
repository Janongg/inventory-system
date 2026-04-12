<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../frontend/index.html");
    exit();
}
?>
