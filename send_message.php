<?php
session_start();
require_once 'config.php';

$sender_id = $_SESSION['user_id'];
$message = mysqli_real_escape_string($conn, $_POST['message']);
$file_type = 'none';
$file_url = '';

if (isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $allowed_types = [
        'image' => ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'],
        'video' => ['video/mp4', 'video/webm', 'video/ogg'],
        'audio' => ['audio/mpeg', 'audio/ogg', 'audio/wav']
    ];
    $file_name = time() . '_' . $file['name'];
    $file_path = 'uploads/' . $file_name;
    
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        $file_url = $file_path;
        $mime_type = $file['type'];
        
        if (in_array($mime_type, $allowed_types['image'])) {
            $file_type = 'image';
        } else if (in_array($mime_type, $allowed_types['video'])) {
            $file_type = 'video';
        } else if (in_array($mime_type, $allowed_types['audio'])) {
            $file_type = 'audio';
        } else {
            $file_url = $file['name'];
            if (preg_match("/^https?:\/\//i", $file_url)) {
                $file_type = 'link';
            } else {
                $file_type = 'none';
                $file_url = '';
            }
        }
    }
} else if (!empty($message) && preg_match("/^https?:\/\//i", $message)) {
    $file_type = 'link';
    $file_url = $message;
    $message = '';
}

$query = "INSERT INTO messages (sender_id, message, file_url, file_type) 
          VALUES ($sender_id, '$message', '$file_url', '$file_type')";
mysqli_query($conn, $query);

echo 'success';
?> 