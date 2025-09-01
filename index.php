<?php
// index.php - Unchanged from previous
// Main chat interface file (updated for authentication)
// Uses JS for redirection if not logged in
// Loads history per user_id
 
session_start();
include 'db.php';
 
if (!isset($_SESSION['user_id'])) {
    echo '<script>window.location.href = "login.php";</script>';
    exit;
}
 
$user_id = $_SESSION['user_id'];
 
// Load chat history
$stmt = $pdo->prepare("SELECT role, message FROM chats WHERE user_id = :user_id ORDER BY timestamp ASC");
$stmt->execute(['user_id' => $user_id]);
$history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chatbot - ChatGPT Clone</title>
    <style>
        /* Internal CSS - Amazing, real-looking, professional design (unchanged, added logout style) */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #333;
        }
        .chat-container {
            width: 100%;
            max-width: 800px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 90vh;
        }
        .chat-header {
            background: #4a90e2;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 1.2em;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
            position: relative;
        }
        .logout-btn {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            padding: 8px 12px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.9em;
            transition: background 0.2s;
        }
        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        .chat-window {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: #f9f9f9;
            display: flex;
            flex-direction: column;
        }
        .message {
            max-width: 70%;
            margin-bottom: 15px;
            padding: 12px 18px;
            border-radius: 20px;
            line-height: 1.4;
            position: relative;
            animation: fadeIn 0.3s ease;
        }
        .user-message {
            align-self: flex-end;
            background: #4a90e2;
            color: white;
            border-bottom-right-radius: 5px;
        }
        .assistant-message {
            align-self: flex-start;
            background: #e5e5ea;
            color: #333;
            border-bottom-left-radius: 5px;
        }
        .input-area {
            display: flex;
            padding: 15px;
            border-top: 1px solid #ddd;
            background: white;
        }
        #user-input {
            flex: 1;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 25px;
            font-size: 1em;
            margin-right: 10px;
            outline: none;
            transition: border 0.2s;
        }
        #user-input:focus {
            border-color: #4a90e2;
        }
        #send-btn {
            background: #4a90e2;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.2s;
        }
        #send-btn:hover {
            background: #357bd8;
        }
        .loading {
            text-align: center;
            color: #999;
            font-style: italic;
            margin: 10px 0;
            display: none;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        /* Responsive design */
        @media (max-width: 600px) {
            .chat-container {
                height: 100vh;
                border-radius: 0;
            }
            .chat-header {
                font-size: 1em;
            }
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            AI Chatbot
            <button class="logout-btn" onclick="window.location.href = 'logout.php';">Logout</button>
        </div>
        <div class="chat-window" id="chat-window">
            <?php foreach ($history as $msg): ?>
                <div class="message <?php echo $msg['role'] === 'user' ? 'user-message' : 'assistant-message'; ?>">
                    <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                </div>
            <?php endforeach; ?>
            <div class="loading" id="loading">Generating response...</div>
        </div>
        <div class="input-area">
            <input type="text" id="user-input" placeholder="Type your message...">
            <button id="send-btn">Send</button>
        </div>
    </div>
 
    <script>
        // Internal JS - Handles message sending, AJAX to backend, loading, and auto-scroll (unchanged)
        const chatWindow = document.getElementById('chat-window');
        const userInput = document.getElementById('user-input');
        const sendBtn = document.getElementById('send-btn');
        const loading = document.getElementById('loading');
 
        function addMessage(role, message) {
            const msgDiv = document.createElement('div');
            msgDiv.classList.add('message', role === 'user' ? 'user-message' : 'assistant-message');
            msgDiv.innerHTML = message.replace(/\n/g, '<br>');
            chatWindow.appendChild(msgDiv);
            chatWindow.scrollTop = chatWindow.scrollHeight;
        }
 
        function sendMessage() {
            const message = userInput.value.trim();
            if (!message) return;
 
            addMessage('user', message);
            userInput.value = '';
            loading.style.display = 'block';
            chatWindow.scrollTop = chatWindow.scrollHeight;
 
            fetch('process.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `message=${encodeURIComponent(message)}`
            })
            .then(response => response.json())
            .then(data => {
                loading.style.display = 'none';
                if (data.error) {
                    addMessage('assistant', 'Error: ' + data.error);
                } else {
                    addMessage('assistant', data.response);
                }
            })
            .catch(error => {
                loading.style.display = 'none';
                addMessage('assistant', 'Error: Failed to get response.');
                console.error(error);
            });
        }
 
        sendBtn.addEventListener('click', sendMessage);
        userInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendMessage();
        });
 
        // Auto-scroll on load
        chatWindow.scrollTop = chatWindow.scrollHeight;
    </script>
</body>
</html>
