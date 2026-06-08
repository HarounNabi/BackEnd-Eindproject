<?php
session_start();
$_SESSION['user'] = 'Gast';
header("Location: home.php");
exit();
