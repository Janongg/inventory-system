<?php
session_start();
session_destroy();
header("Location: ../frontend/index.html?msg=logged_out");
exit();
?>
