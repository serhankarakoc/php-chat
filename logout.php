<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    // Kullanıcıyı çevrimdışı yap
    $query = "UPDATE users SET online_status = 'offline' WHERE id = $user_id";
    mysqli_query($conn, $query);
}

// Oturumu sonlandır
session_destroy();
header('Location: login.php');
exit();
?> 