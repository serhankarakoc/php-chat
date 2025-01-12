<?php
session_start();
require_once 'config.php';

$user_id = $_SESSION['user_id'];
$query = "UPDATE users SET last_seen = NOW(), online_status = 'online' WHERE id = $user_id";
mysqli_query($conn, $query);

// 30 saniyeden fazla süre geçen kullanıcıları çevrimdışı yap
$query = "UPDATE users SET online_status = 'offline' 
          WHERE last_seen < NOW() - INTERVAL 30 SECOND";
mysqli_query($conn, $query);

echo 'success';
?> 