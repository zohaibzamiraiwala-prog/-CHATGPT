<?php
// signup.php - Unchanged from previous
// User signup page with form and processing
// Internal CSS for amazing, real-looking design
// Processes POST, hashes password, checks for unique username
 
session_start();
include 'db.php';
 
$error = '';
$success = false;
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
 
    if (empty($username) || empty($password)) {
        $error = 'Username and password are required.';
    } elseif (strlen($username) < 3 || strlen($password) < 6) {
        $error = 'Username must be at least 3 characters, password at least 6.';
    } else {
        // Check if username exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        if ($stmt->fetch()) {
            $error = 'Username already taken.';
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (:username, :password_hash)");
            if ($stmt->execute(['username' => $username, 'password_hash' => $password_hash])) {
                $success = true;
            } else {
                $error = 'Failed to create account. Try again.';
            }
        }
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - AI Chatbot</title>
    <style>
        /* Internal CSS - Professional, engaging design */
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
        <h2>Create Account</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success">Account created! Redirecting to login...</div>
            <script>setTimeout(() => { window.location.href = 'login.php'; }, 2000);</script>
        <?php else: ?>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Signup</button>
            </form>
            <a href="login.php">Already have an account? Login</a>
        <?php endif; ?>
    </div>
</body>
</html>
