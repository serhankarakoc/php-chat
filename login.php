<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    if (isset($_POST['action']) && $_POST['action'] == 'register') {
        // Kayıt işlemi
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')";
        
        if (mysqli_query($conn, $query)) {
            $user_id = mysqli_insert_id($conn);
            $_SESSION['user_id'] = $user_id;

            // Kullanıcıyı online yap
            $update_query = "UPDATE users SET last_seen = NOW(), online_status = 'online' WHERE id = $user_id";
            mysqli_query($conn, $update_query);

            header('Location: index.php');
            exit();
        } else {
            $error = 'Kayıt işlemi başarısız oldu.';
        }
    } else {
        // Giriş işlemi
        $query = "SELECT id, password FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $query);
        
        if ($user = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];

                // Kullanıcıyı online yap
                $user_id = $user['id'];
                $update_query = "UPDATE users SET last_seen = NOW(), online_status = 'online' WHERE id = $user_id";
                mysqli_query($conn, $update_query);

                header('Location: index.php');
                exit();
            } else {
                $error = 'Hatalı şifre!';
            }
        } else {
            $error = 'Kullanıcı bulunamadı!';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Giriş</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #d5dbd8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            width: 300px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #075e54;
        }
        input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #075e54;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 10px;
        }
        button:hover {
            background: #128c7e;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 style="color: #075e54; text-align: center;">Giriş Yap</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Kullanıcı Adı</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Şifre</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit">Giriş Yap</button>
        </form>
		<!--
        <div class="login-link" style="text-align: center; margin-top: 15px;">
            Hesabınız yok mu? <a href="register.php" style="color: #075e54; text-decoration: none;">Kayıt Ol</a>
        </div>
		-->
    </div>
</body>
</html>