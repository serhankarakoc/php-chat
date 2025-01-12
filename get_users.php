<?php
session_start();
require_once 'config.php';

$user_id = $_SESSION['user_id'];
$query = "SELECT id, username, online_status, last_seen FROM users WHERE id != $user_id";
$result = mysqli_query($conn, $query);

$users = [];
while ($user = mysqli_fetch_assoc($result)) {
    $users[] = $user;
}

echo json_encode($users);
?> 