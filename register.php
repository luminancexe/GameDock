<?php
require_once "includes/connection.php";
include "includes/header.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST["fullname"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $password = $_POST["password"] ?? "";

    if (empty($fullname) || empty($email) || empty($password)) {
        $error = "Please fill all required fields.";
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email is already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (fullname, email, phone, password, created_at) VALUES (?, ?, ?, ?, NOW())");
            if ($stmt->execute([$fullname, $email, $phone, $hashed_password])) {
                $success = "Registration successful! You can now <a href='login.php'>Login</a>.";
            } else {
                $error = "Something went wrong. Please try again later.";
            }
        }
    }
}
?>

<div class="container">
    <div class="form-container">
        <h2>Register</h2>
        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php else: ?>
            <form action="register.php" method="POST">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="fullname" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" class="form-control" required
                        pattern="[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}"
                        title="Enter a valid email address, e.g. name@example.com">
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control"
                        pattern="\+?[0-9\s]{7,15}"
                        title="Digits and spaces only, with an optional leading +, 7–15 characters">
                </div>
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" class="form-control" required
                        pattern="(?=.*[A-Za-z])(?=.*\d).{8,}"
                        title="At least 8 characters, including at least one letter and one number">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Register</button>
            </form>
            <p class="text-center mt-4">Already have an account? <a href="login.php">Login here</a>.</p>
        <?php endif; ?>
    </div>
</div>

<?php include "includes/footer.php"; ?>
