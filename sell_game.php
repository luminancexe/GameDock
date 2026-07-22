<?php
require_once "includes/connection.php";
require_once "includes/auth_check.php";
requireLogin();

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $platform = $_POST['platform'];
    $product_type = $_POST['product_type'];
    $asking_price = $_POST['asking_price'];
    $description = trim($_POST['description']);
    
    // Simple file upload logic
    $imagePath = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $newname = uniqid() . "." . $ext;
        if (move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $newname)) {
            $imagePath = $newname;
        }
    }
    
    $stmt = $pdo->prepare("INSERT INTO sell_games (user_id, title, platform, product_type, asking_price, description, image, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'Submitted', NOW())");
    if ($stmt->execute([$_SESSION['user_id'], $title, $platform, $product_type, $asking_price, $description, $imagePath])) {
        $message = "Game submitted successfully! It will appear in your profile.";
    } else {
        $message = "Error submitting game.";
    }
}
include "includes/header.php";
?>

<div class="container">
    <div class="form-container">
        <h2>Sell Your Game</h2>
        <?php if($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <form action="sell_game.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Game Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Platform</label>
                <select name="platform" class="form-control" required>
                    <option value="PC">PC</option>
                    <option value="PS4">PS4</option>
                    <option value="PS5">PS5</option>
                    <option value="Xbox Series X">Xbox Series X</option>
                    <option value="Xbox Series S">Xbox Series S</option>
                </select>
            </div>
            <div class="form-group">
                <label>Product Type</label>
                <select name="product_type" class="form-control" required>
                    <option value="Game Key">Game Key</option>
                    <option value="Disc">Disc</option>
                    <option value="Account">Account</option>
                </select>
            </div>
            <div class="form-group">
                <label>Asking Price ($)</label>
                <input type="number" step="0.01" name="asking_price" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label>Game Image</label>
                <input type="file" name="image" class="form-control" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Submit Game</button>
        </form>
    </div>
</div>

<?php include "includes/footer.php"; ?>
