<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>...</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #d5dbd8;
        }
        .container {
            display: flex;
            height: 100vh;
        }
        .chat-container {
            width: 100%;
            display: flex;
            flex-direction: column;
        }
        .chat-header {
            background: #075e54;
            color: white;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .online-users {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .user-status {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            margin: 0 5px;
        }
        .user-status.online {
            background: #4CAF50;
        }
        .user-status.offline {
            background: #f44336;
        }
        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: #e5ddd5;
        }
        .message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 10px;
            max-width: 60%;
        }
        .message-time {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
            text-align: right;
        }
        .message.sent {
            background: #dcf8c6;
            margin-left: auto;
        }
        .message.received {
            background: white;
        }
        .chat-input {
            padding: 20px;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            border-top: 1px solid #ddd;
            gap: 10px;
            position: relative;
        }
        .chat-input input {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 20px;
            padding-left: 20px;
            font-size: 15px;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .chat-input input:focus {
            outline: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        .chat-input button {
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 50%;
            background: #075e54;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .chat-input button:hover {
            background: #128c7e;
            transform: scale(1.05);
        }
        .chat-input button i {
            font-size: 18px;
        }
        .file-preview {
            position: absolute;
            bottom: 100%;
            left: 0;
            right: 0;
            background: white;
            padding: 15px;
            border-top: 1px solid #ddd;
            display: none;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
        }
        .file-preview img {
            max-height: 150px;
            border-radius: 8px;
            display: block;
            margin: 0 auto;
        }
        .file-preview video {
            max-height: 150px;
            border-radius: 8px;
            display: block;
            margin: 0 auto;
        }
        .file-preview audio {
            width: 100%;
            margin: 10px 0;
        }
        .file-preview .close-preview {
            position: absolute;
            right: 10px;
            top: 10px;
            background: rgba(0,0,0,0.5);
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .file-preview .close-preview:hover {
            background: rgba(0,0,0,0.7);
            transform: scale(1.1);
        }
        .user-list {
            list-style: none;
            padding: 0;
        }
        .user-item {
            padding: 15px;
            border-bottom: 1px solid #ddd;
            cursor: pointer;
        }
        .user-item:hover {
            background: #f5f5f5;
        }
        .online-status {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
        .online {
            background: #4CAF50;
        }
        .offline {
            background: #9E9E9E;
        }
        .sidebar-header {
            background: #075e54;
            color: white;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .online-users-title {
            font-size: 16px;
            font-weight: bold;
        }
        .chat-header {
            background: #075e54;
            color: white;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logout-btn {
            background: #4CAF50;
            border: none;
            color: white;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .logout-btn:hover {
            background: rgba(255,255,255,0.1);
            border-radius: 5px;
        }
        .user-item {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            border-bottom: 1px solid #ddd;
            cursor: pointer;
        }
        .user-item:hover {
            background: #f5f5f5;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #128c7e;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
        }
        .scroll-bottom-btn {
            position: fixed;
            right: 20px;
            bottom: 100px;
            background: #075e54;
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            display: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
            transition: all 0.3s ease;
        }
        .scroll-bottom-btn:hover {
            background: #128c7e;
            transform: scale(1.1);
        }
        .scroll-bottom-btn.visible {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .new-message-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .load-more {
            text-align: center;
            margin: 10px 0;
        }
        .load-more button {
            background: #075e54;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 15px;
            cursor: pointer;
            font-size: 14px;
        }
        .load-more button:hover {
            background: #128c7e;
        }
        .user-id {
            background: #075e54;
            color: white;
            padding: 5px;
            border-radius: 50%;
            font-size: 11px;
            width: 15px;
            height: 15px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .message.sent .user-id {
            background: #128c7e;
        }
        .message.received .user-id {
            background: #075e54;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="chat-container">
            <div class="chat-header">
                <div class="online-users" id="online-users">
                    <!-- Çevrimiçi kullanıcılar buraya gelecek -->
                </div>
                <button class="logout-btn" onclick="logout()">Çıkış Yap</button>
            </div>
            <div class="chat-messages" id="chat-messages">
                <!-- Mesajlar AJAX ile yüklenecek -->
            </div>
            <button id="scroll-bottom-btn" class="scroll-bottom-btn" onclick="scrollToBottom(true)" title="Son mesaja git">
                <i class="fas fa-chevron-down"></i>
                <span id="new-message-badge" class="new-message-badge" style="display: none">0</span>
            </button>
            <div class="chat-input">
                <div class="file-preview" id="file-preview">
                    <button class="close-preview" onclick="clearFileInput()">
                        <i class="fas fa-times"></i>
                    </button>
                    <div id="preview-content"></div>
                </div>
                <input type="file" id="file-input" style="display: none">
                <button onclick="document.getElementById('file-input').click()" title="Dosya ekle">
                    <i class="fas fa-paperclip"></i>
                </button>
                <input type="text" id="message-input" placeholder="Mesaj yazın...">
                <button onclick="sendMessage()" title="Gönder">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let lastMessageId = 0;
        let isScrolledToBottom = true;
        let newMessageCount = 0;
        let userScrolling = false;
        let currentPage = 0;
        let isLoadingMessages = false;

        $('#chat-messages').on('scroll', function() {
            const chatMessages = document.getElementById('chat-messages');
            isScrolledToBottom = Math.abs(chatMessages.scrollHeight - chatMessages.scrollTop - chatMessages.clientHeight) < 10;
            
            if (isScrolledToBottom) {
                $('#scroll-bottom-btn').hide();
                newMessageCount = 0;
                $('#new-message-badge').hide();
                isScrolledToBottom = true;
            } else {
                isScrolledToBottom = false;
                $('#scroll-bottom-btn').fadeIn();
            }
        });

        function updateOnlineStatus() {
            $.ajax({
                url: 'update_status.php',
                method: 'POST',
                success: function() {
                    loadUsers();
                }
            });
        }

        function loadUsers() {
            $.ajax({
                url: 'get_users.php',
                success: function(response) {
                    const users = JSON.parse(response);
                    const usersDiv = $('#online-users');
                    usersDiv.empty();
                    
                    users.forEach(user => {
                        usersDiv.append(`
                            <span class="user-status ${user.online_status}">
                                ${user.id}
                            </span>
                        `);
                    });
                }
            });
        }

        function loadMessages() {
            if (isLoadingMessages) return;
            isLoadingMessages = true;
            $.ajax({
                url: 'get_messages.php',
                data: {
                    last_id: lastMessageId,
                    page: currentPage
                },
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.messages) {
                        if (lastMessageId === 0) {
                            $('#chat-messages').empty();
                            $('#chat-messages').prepend(`
                                <div class="load-more" id="load-more">
                                    <button onclick="loadPreviousMessages()">
                                        <i class="fas fa-chevron-up"></i> Önceki Mesajlar
                                    </button>
                                </div>
                            `);
                            if (data.has_more) {
                                $('#load-more').show();
                            } else {
                                $('#load-more').hide();
                            }
                        }

                        data.messages.forEach(message => {
                            appendMessage(message);
                            lastMessageId = Math.max(lastMessageId, message.id);
                        });

                        if (lastMessageId === 0) {
                            scrollToBottom(false);
                        }
                    }
                    isLoadingMessages = false;
                }
            });
        }

        function loadPreviousMessages() {
            if (isLoadingMessages) return;
            isLoadingMessages = true;
            
            const chatMessages = document.getElementById('chat-messages');
            const oldHeight = chatMessages.scrollHeight;
            const oldScroll = chatMessages.scrollTop;
            currentPage++;
            
            $.ajax({
                url: 'get_messages.php',
                data: {
                    last_id: 0,
                    page: currentPage
                },
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.messages && data.messages.length > 0) {
                        let messagesHtml = '';
                        data.messages.forEach(message => {
                            const messageClass = message.sender_id == <?php echo $user_id; ?> ? 'sent' : 'received';
                            let messageContent = message.message;
                            let isMediaMessage = false;

                            if (message.file_type === 'image') {
                                messageContent = `<img src="${message.file_url}" style="max-width: 200px; display: block; cursor: pointer" onclick="window.open(this.src)">`;
                                isMediaMessage = true;
                            } else if (message.file_type === 'video') {
                                messageContent = `<video controls style="max-width: 200px;"><source src="${message.file_url}" type="video/mp4"></video>`;
                                isMediaMessage = true;
                            } else if (message.file_type === 'audio') {
                                messageContent = `<audio controls><source src="${message.file_url}" type="audio/mpeg"></audio>`;
                                isMediaMessage = true;
                            } else if (message.file_type === 'link') {
                                let url = message.file_url;
                                if (!url.match(/^https?:\/\//i)) {
                                    url = 'http://' + url;
                                }
                                messageContent = `<a href="${url}" target="_blank" style="color: #2196F3; text-decoration: none; display: block; padding: 5px 10px; background: rgba(33, 150, 243, 0.1); border-radius: 5px;">
                                    <i class="fas fa-external-link-alt"></i> ${message.file_url}
                                </a>`;
                            }

                            const messageDate = new Date(message.created_at);
                            const formattedDate = messageDate.toLocaleString('tr-TR', {
                                year: 'numeric',
                                month: '2-digit',
                                day: '2-digit',
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit'
                            });

                            if (isMediaMessage) {
                                messagesHtml += `
                                    <div style="display: flex; flex-direction: column; align-items: ${messageClass === 'sent' ? 'flex-end' : 'flex-start'}; margin: 10px 0;">
                                        ${messageContent}
                                        <div class="message-time" style="margin-top: 5px; text-align: ${messageClass === 'sent' ? 'right' : 'left'}; width: 200px;">
                                            ${formattedDate}
                                        </div>
                                    </div>`;
                            } else {
                                messagesHtml += `
                                    <div class="message ${messageClass}">
                                        ${messageContent}
                                        <div class="message-time">${formattedDate}</div>
                                    </div>`;
                            }
                        });

                        $('#load-more').after(messagesHtml);

                        const newHeight = chatMessages.scrollHeight;
                        const heightDiff = newHeight - oldHeight;
                        chatMessages.scrollTop = oldScroll + heightDiff;

                        if (data.has_more) {
                            $('#load-more button').html(`
                                <i class="fas fa-chevron-up"></i> Önceki Mesajlar
                            `);
                        } else {
                            $('#load-more').hide();
                        }
                    }
                    isLoadingMessages = false;
                },
                error: function() {
                    isLoadingMessages = false;
                    currentPage--;
                }
            });
        }

        function appendMessage(message, container = null) {
            const messageClass = message.sender_id == <?php echo $user_id; ?> ? 'sent' : 'received';
            let messageContent = '';
            let isMediaMessage = false;
            const wasScrolledToBottom = isScrolledToBottom;

            if (message.message) {
                messageContent = message.message;
            }

            const userInfo = `<small style="color: #666; display: block; margin-bottom: 5px;">
                <span class="user-id">${message.sender_id}</span>
            </small>`;

            if (message.file_type === 'image') {
                messageContent = `<img src="${message.file_url}" style="max-width: 200px; display: block; cursor: pointer" onclick="window.open(this.src)">`;
                isMediaMessage = true;
            } else if (message.file_type === 'video') {
                messageContent = `<video controls style="max-width: 200px;">
                    <source src="${message.file_url}" type="video/mp4">
                    Tarayıcınız video elementini desteklemiyor.
                </video>`;
                isMediaMessage = true;
            } else if (message.file_type === 'audio') {
                messageContent = `<audio controls>
                    <source src="${message.file_url}" type="audio/mpeg">
                    Tarayıcınız audio elementini desteklemiyor.
                </audio>`;
                isMediaMessage = true;
            } else if (message.file_type === 'link') {
                let url = message.file_url;
                if (!url.match(/^https?:\/\//i)) {
                    url = 'http://' + url;
                }
                messageContent = `<a href="${url}" target="_blank" style="color: #2196F3; text-decoration: none; display: block; padding: 5px 10px; background: rgba(33, 150, 243, 0.1); border-radius: 5px;">
                    <i class="fas fa-external-link-alt"></i> ${message.file_url}
                </a>`;
            }

            const messageDate = new Date(message.created_at);
            const formattedDate = messageDate.toLocaleString('tr-TR', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });

            if (isMediaMessage) {
                $('#chat-messages').append(`
                    <div style="display: flex; flex-direction: column; align-items: ${messageClass === 'sent' ? 'flex-end' : 'flex-start'}; margin: 10px 0;">
                        ${userInfo}
                        ${messageContent}
                        <div class="message-time" style="margin-top: 5px; text-align: ${messageClass === 'sent' ? 'right' : 'left'}; width: 200px;">
                            ${formattedDate}
                        </div>
                    </div>
                `);
            } else {
                const messageHtml = `
                    <div class="message ${messageClass}">
                        ${userInfo}
                        ${messageContent}
                        <div class="message-time">${formattedDate}</div>
                    </div>
                `;
                
                if (container) {
                    container.append(messageHtml);
                } else {
                    $('#chat-messages').append(messageHtml);
                }
            }

            if (wasScrolledToBottom || message.sender_id == <?php echo $user_id; ?>) {
                scrollToBottom(false);
            } else {
                newMessageCount++;
                $('#scroll-bottom-btn').fadeIn();
                $('#new-message-badge').text(newMessageCount).show();
            }
        }

        function isValidUrl(string) {
            return string.match(/^https?:\/\//i) !== null;
        }

        function sendMessage() {
            const message = $('#message-input').val();
            const fileInput = $('#file-input')[0];
            const file = fileInput.files[0];

            if (message && message.match(/^https?:\/\//i)) {
                const formData = new FormData();
                formData.append('message', message);
                $.ajax({
                    url: 'send_message.php',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function() {
                        $('#message-input').val('');
                        loadMessages();
                    }
                });
                return;
            }

            if (!message && !file) return;

            const formData = new FormData();
            formData.append('message', message);
            if (file) {
                formData.append('file', file);
            }

            $.ajax({
                url: 'send_message.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function() {
                    $('#message-input').val('');
                    fileInput.value = '';
                    $('.file-preview').slideUp();
                    loadMessages();
                }
            });
        }

        function scrollToBottom(animate = true) {
            const chatMessages = document.getElementById('chat-messages');
            if (animate) {
                $('#chat-messages').animate({
                    scrollTop: chatMessages.scrollHeight
                }, 300, function() {
                    $('#scroll-bottom-btn').hide();
                    isScrolledToBottom = true;
                });
            } else {
                chatMessages.scrollTop = chatMessages.scrollHeight;
                $('#scroll-bottom-btn').hide();
                isScrolledToBottom = true;
            }

            newMessageCount = 0;
            $('#new-message-badge').hide();
        }

        function initialLoad() {
            currentPage = 0;
            lastMessageId = 0;
            loadMessages();
            scrollToBottom(false);
        }

        $(document).ready(function() {
            loadUsers();
            initialLoad();
            setInterval(updateOnlineStatus, 10000);
            setInterval(loadMessages, 1000);
        });

        function logout() {
            window.location.href = 'logout.php';
        }

        $('#message-input').keypress(function(e) {
            if (e.which === 13) {  // Enter tuşu
                e.preventDefault();
                sendMessage();
            }
        });

        // Dosya seçildiğinde önizleme göster
        $('#file-input').change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let previewContent = '';
                    if (file.type.startsWith('image/')) {
                        previewContent = `<img src="${e.target.result}" alt="Önizleme">`;
                    } else if (file.type.startsWith('video/')) {
                        previewContent = `<video src="${e.target.result}" style="max-height: 150px;" controls></video>`;
                    } else if (file.type.startsWith('audio/')) {
                        previewContent = `<audio src="${e.target.result}" controls></audio>`;
                    } else {
                        previewContent = `<div style="padding: 10px; text-align: center;">
                            <i class="fas fa-file" style="font-size: 40px; color: #075e54;"></i>
                            <div style="margin-top: 10px;">${file.name}</div>
                        </div>`;
                    }
                    $('#preview-content').html(previewContent);
                    $('.file-preview').slideDown();
                }
                reader.readAsDataURL(file);
            }
        });

        // Önizlemeyi temizle
        function clearFileInput() {
            $('#file-input').val('');
            $('.file-preview').slideUp();
        }
    </script>
</body>
</html> 
