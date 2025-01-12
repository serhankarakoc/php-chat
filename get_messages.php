<?php
session_start();
require_once 'config.php';

$user_id = $_SESSION['user_id'];
$last_id = $_GET['last_id'];
$page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
$limit = 20;

if ($last_id > 0) {
    // Yeni mesajları getir
    $query = "SELECT messages.*, users.username FROM messages 
              JOIN users ON messages.sender_id = users.id
              WHERE messages.id > $last_id
              ORDER BY messages.id ASC";
} else {
    // Önceki mesajları getir
    $total_query = "SELECT COUNT(*) as total FROM messages";
    $total_result = mysqli_query($conn, $total_query);
    $total_messages = mysqli_fetch_assoc($total_result)['total'];
    
    $offset = $page * $limit;
    $query = "SELECT m.*, u.username 
              FROM (
                  SELECT * FROM messages 
                  ORDER BY id DESC 
                  LIMIT $limit OFFSET $offset
              ) AS m
              JOIN users u ON m.sender_id = u.id
              ORDER BY m.id ASC";
}

$result = mysqli_query($conn, $query);
$messages = [];

while ($message = mysqli_fetch_assoc($result)) {
    $message['created_at'] = date('Y-m-d H:i:s', strtotime($message['created_at']));
    $messages[] = $message;
}

$has_more = isset($total_messages) && 
            !($page === 0 && $total_messages <= $limit) && 
            ($total_messages > ($offset + $limit));

echo json_encode([
    'messages' => $messages,
    'has_more' => $has_more,
    'page' => $page,
    'total' => isset($total_messages) ? $total_messages : count($messages)
]);
?> 