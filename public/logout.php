<?php
require_once '../includes/session.php';
$_SESSION = array();
// session distroy
session_destroy();
// back to login page
header("Location: login.php");
exit;
?>