<?php
require_once "includes/connection.php";
include "includes/header.php";

// Handle new post submission
$success_msg = "";
$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_post']) && isset($_SESSION['user_id'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $user_id = $_SESSION['user_id'];
    
    if (empty($title) || empty($content)) {
        $error_msg = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO forum_posts (user_id, title, content) VALUES (?, ?, ?)");
        if ($stmt->execute([$user_id, $title, $content])) {
            $success_msg = "Post submitted successfully!";
        } else {
            $error_msg = "Error submitting post.";
        }
    }
}

// Fetch all posts
$stmt = $pdo->query("SELECT p.*, u.fullname, u.profile_pic FROM forum_posts p JOIN users u ON p.user_id = u.user_id ORDER BY p.created_at DESC");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <h2 class="text-center" style="color: var(--accent); margin-bottom: 30px;">Community Forum</h2>

    <?php if ($success_msg): ?>
        <div class="alert alert-success"><?php echo $success_msg; ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 1fr 3fr; gap: 40px;">
        <!-- Left Side: Post Form (Only if logged in) -->
        <div>
            <?php if(isset($_SESSION['user_id'])): ?>
                <div class="form-container" style="margin: 0; padding: 20px; box-shadow: none;">
                    <h3 style="margin-top: 0;">Start a Discussion</h3>
                    <form method="POST">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control" required placeholder="What's on your mind?">
                        </div>
                        <div class="form-group">
                            <label>Message</label>
                            <textarea name="content" class="form-control" rows="5" required placeholder="Write your post here..."></textarea>
                        </div>
                        <button type="submit" name="submit_post" class="btn btn-primary w-full">Post to Forum</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="form-container" style="margin: 0; padding: 20px; box-shadow: none; text-align: center;">
                    <h3 style="margin-top: 0; color: var(--warning);">Join the Conversation</h3>
                    <p style="color: #D1D5DB; margin-bottom: 20px;">You must be logged in to post in the community forum.</p>
                    <a href="login.php" class="btn btn-primary">Login / Register</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Side: Posts List -->
        <div>
            <?php if (count($posts) > 0): ?>
                <?php foreach ($posts as $post): ?>
                    <?php $pic = $post['profile_pic'] ?? 'default.png'; ?>
                    <div class="game-card" style="padding: 20px; margin-bottom: 20px; display: flex; gap: 20px;">
                        <div style="flex-shrink: 0; text-align: center;">
                            <img src="uploads/avatars/<?php echo htmlspecialchars($pic); ?>" alt="Avatar" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent);">
                        </div>
                        <div style="flex-grow: 1; display: flex; flex-direction: column;">
                            <h3 style="margin-top: 0; color: var(--white);"><?php echo htmlspecialchars($post['title']); ?></h3>
                            <p style="color: #D1D5DB; margin-bottom: 15px;"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 10px; margin-top: auto;">
                                <span style="color: var(--accent); font-weight: bold; font-size: 0.9em;">By <?php echo htmlspecialchars($post['fullname']); ?></span>
                                <span style="color: #9CA3AF; font-size: 0.8em;"><?php echo date("F j, Y, g:i a", strtotime($post['created_at'])); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="game-card" style="padding: 30px; text-align: center;">
                    <h3 style="color: #9CA3AF;">No posts yet.</h3>
                    <p style="color: #6B7280;">Be the first to start a discussion!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
