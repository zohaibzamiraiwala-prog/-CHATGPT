<?php
// login.php - Unchanged from previous
// User login page with form and processing
// Internal CSS for amazing, real-looking design
// Processes POST, verifies password, sets session
 
session_start();
include 'db.php';
 
$error = '';
$success = false;
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
 
    if (empty($username) || empty($password)) {
        $error = 'Username and password are required.';
    } else {
        $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $success = true;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AI Chatbot</title>
    <style>
        /* Internal CSS - Same as signup for consistency */
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
        .form-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h2 {
            color: #4a90e2;
            margin-bottom: 20px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 25px;
            font-size: 1em;
            outline: none;
            transition: border 0.2s;
        }
        input:focus {
            border-color: #4a90e2;
        }
        button {
            background: #4a90e2;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
            margin-top: 10px;
            transition: background 0.2s;
        }
        button:hover {
            background: #357bd8;
        }
        .error {
            color: red;
            margin: 10px 0;
        }
        .success {
            color: green;
            margin: 10px 0;
        }
        a {
            color: #4a90e2;
            text-decoration: none;
            display: block;
            margin-top: 20px;
        }
        a:hover {
            text-decoration: underline;
        }
        /* Responsive */
        @media (max-width: 600px) {
            .form-container {
                border-radius: 0;
                box-shadow: none;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Login</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success">Logged in! Redirecting...</div>
            <script>setTimeout(() => { window.location.href = 'index.php'; }, 2000);</script>
        <?php else: ?>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
            <a href="signup.php">Don't have an account? Signup</a>
        <?php endif; ?>
    </div>
</body>
</html>
