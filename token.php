<?php
session_start();
$token = uniqid() . 'book';
$_SESSION['token'] = $token;
echo $token;
?>