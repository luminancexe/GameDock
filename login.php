<?php
require_once "includes/connection.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if (empty($email) || empty($password)) {
        $error = "Please fill all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT user_id, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];

                if (isset($_POST['remember_me'])) {
                    $token = bin2hex(random_bytes(32));
                    $tokenHash = hash('sha256', $token);
                    $expires = date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 30); // 30 days

                    $stmt = $pdo->prepare("UPDATE users SET remember_token_hash = ?, remember_token_expires = ? WHERE user_id = ?");
                    $stmt->execute([$tokenHash, $expires, $user['user_id']]);

                    setcookie('remember_token', $user['user_id'] . ':' . $token, [
                        'expires' => time() + 60 * 60 * 24 * 30,
                        'path' => '/',
                        'httponly' => true,
                        'samesite' => 'Lax',
                    ]);
                }

                header("Location: profile.php");
                exit;
            } else {
                $error = "Invalid credentials.";
            }
        } else {
            $error = "Invalid credentials.";
        }
    }
}

// Header is included after POST handling so a successful login's redirect
// (header()) can still fire — it can't run once HTML output has started.
include "includes/header.php";
?>

<div class="container">
    <div class="form-container">
        <h2>Login</h2>
        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required
                    pattern="[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}"
                    title="Enter a valid email address, e.g. name@example.com">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
                <input type="checkbox" name="remember_me" id="remember_me" style="width: auto;">
                <label for="remember_me" style="margin: 0; font-weight: normal;">Remember me for 30 days</label>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
        </form>
        <p class="text-center mt-4">Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>
</div>

<?php include "includes/footer.php"; ?>
