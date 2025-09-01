<?php
// process.php - Updated to use xAI Grok API instead of OpenAI for AI responses
// Connects to xAI API, stores in DB per user_id
// You need to replace 'YOUR_XAI_API_KEY_HERE' with your actual xAI API key from https://console.x.ai/
// Sign up at https://accounts.x.ai/ and generate key at https://console.x.ai/team/default/api-keys
// Uses grok-3-mini as it's cheaper; change to grok-4-0709 for advanced features (requires subscription)
 
session_start();
include 'db.php';
 
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'Not authenticated']));
}
 
$user_id = $_SESSION['user_id'];
 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $user_message = trim($_POST['message']);
 
    // Store user message
    $stmt = $pdo->prepare("INSERT INTO chats (user_id, role, message) VALUES (:user_id, 'user', :message)");
    $stmt->execute(['user_id' => $user_id, 'message' => $user_message]);
 
    // Fetch history for context (last 10 messages for context)
    $stmt = $pdo->prepare("SELECT role, message FROM chats WHERE user_id = :user_id ORDER BY timestamp DESC LIMIT 10");
    $stmt->execute(['user_id' => $user_id]);
    $recent_history = array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC)); // Reverse to chronological
 
    // Prepare messages for xAI API
    $messages = [];
    foreach ($recent_history as $msg) {
        $messages[] = ['role' => $msg['role'], 'content' => $msg['message']];
    }
    // Add the latest user message if not included
    if (end($messages)['content'] !== $user_message) {
        $messages[] = ['role' => 'user', 'content' => $user_message];
    }
 
    // xAI API call
    $api_key = 'YOUR_XAI_API_KEY_HERE'; // Replace with your actual xAI API key
    $url = 'https://api.x.ai/v1/chat/completions';
 
    // Check for placeholder key to avoid error
    if ($api_key === 'YOUR_XAI_API_KEY_HERE') {
        $ai_response = 'Hey ZOHAIB How Are You ?';
        // Store as assistant message
        $stmt = $pdo->prepare("INSERT INTO chats (user_id, role, message) VALUES (:user_id, 'assistant', :message)");
        $stmt->execute(['user_id' => $user_id, 'message' => $ai_response]);
        echo json_encode(['response' => $ai_response]);
        exit;
    }
 
    $data = [
        'model' => 'grok-3-mini', // Use 'grok-4-0709' for more advanced model (may require higher subscription)
        'messages' => $messages,
        'temperature' => 0.7,
        'max_tokens' => 500
    ];
 
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key
    ]);
 
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
 
    if ($http_code === 200) {
        $result = json_decode($response, true);
        $ai_response = trim($result['choices'][0]['message']['content']);
 
        // Store AI response
        $stmt = $pdo->prepare("INSERT INTO chats (user_id, role, message) VALUES (:user_id, 'assistant', :message)");
        $stmt->execute(['user_id' => $user_id, 'message' => $ai_response]);
 
        echo json_encode(['response' => $ai_response]);
    } else {
        echo json_encode(['error' => 'API request failed: ' . $response]);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>```
