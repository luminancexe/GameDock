<?php
require_once "includes/connection.php";
require_once "includes/reaction_icons.php";
require_once "includes/avatar_gallery_data.php";
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

// Reaction counts per post, grouped by type
$reactionCounts = [];
$stmt = $pdo->query("SELECT post_id, reaction_type, COUNT(*) AS c FROM forum_reactions GROUP BY post_id, reaction_type");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $reactionCounts[$row['post_id']][$row['reaction_type']] = (int)$row['c'];
}

// The current user's own reaction per post, if logged in
$userReactions = [];
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT post_id, reaction_type FROM forum_reactions WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $userReactions[$row['post_id']] = $row['reaction_type'];
    }
}

// Comments per post, oldest first within each post
$comments = [];
$stmt = $pdo->query("SELECT c.comment_id, c.post_id, c.user_id, c.content, c.created_at, u.fullname, u.profile_pic FROM forum_comments c JOIN users u ON c.user_id = u.user_id ORDER BY c.created_at ASC");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $comments[$row['post_id']][] = $row;
}
?>

<div class="container mt-4">
    <h2 class="text-center" style="color: var(--accent); margin-bottom: 1.875rem;">Community Forum</h2>

    <?php if ($success_msg): ?>
        <div class="alert alert-success"><?php echo $success_msg; ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 1fr 3fr; gap: 2.5rem;">
        <!-- Left Side: Post Form (Only if logged in) -->
        <div>
            <?php if(isset($_SESSION['user_id'])): ?>
                <div class="form-container" style="margin: 0; padding: 1.25rem; box-shadow: none;">
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
                        <button type="submit" name="submit_post" class="btn btn-primary" style="width: 100%;">Post to Forum</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="form-container" style="margin: 0; padding: 1.25rem; box-shadow: none; text-align: center;">
                    <h3 style="margin-top: 0; color: var(--accent);">Join the Conversation</h3>
                    <p style="color: var(--muted); margin-bottom: 1.25rem;">You must be logged in to post in the community forum.</p>
                    <a href="login.php" class="btn btn-primary">Login / Register</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Side: Posts List -->
        <div>
            <?php if (count($posts) > 0): ?>
                <?php foreach ($posts as $post): ?>
                    <?php
                    $pic = $post['profile_pic'] ?? 'default.png';
                    if ($pic === 'default.png') {
                        $pic = default_avatar_for_seed($post['post_id']);
                    }
                    ?>
                    <div class="game-card" style="padding: 1.25rem; margin-bottom: 1.25rem; display: flex; gap: 1.25rem;">
                        <div style="flex-shrink: 0; text-align: center;">
                            <img src="uploads/avatars/<?php echo htmlspecialchars($pic); ?>" alt="Avatar" style="width: 3.75rem; height: 3.75rem; border-radius: 50%; object-fit: cover; border: 0.125rem solid var(--accent);">
                        </div>
                        <div style="flex-grow: 1; display: flex; flex-direction: column;">
                            <h3 style="margin-top: 0; color: var(--light-bg);"><?php echo htmlspecialchars($post['title']); ?></h3>
                            <p style="color: var(--muted); margin-bottom: 0.9375rem;"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 0.0625rem solid var(--border); padding-top: 0.625rem; margin-top: auto;">
                                <span style="color: var(--accent); font-weight: bold; font-size: 0.9em;">By <?php echo htmlspecialchars($post['fullname']); ?></span>
                                <span style="color: var(--muted); font-size: 0.8em;"><?php echo date("F j, Y, g:i a", strtotime($post['created_at'])); ?></span>
                            </div>

                            <?php
                            $postId = $post['post_id'];
                            $postCounts = $reactionCounts[$postId] ?? [];
                            $likeTotal = 0;
                            foreach ($LIKE_FAMILY_TYPES as $lt) { $likeTotal += $postCounts[$lt] ?? 0; }
                            $dislikeCount = $postCounts['dislike'] ?? 0;
                            $myReaction = $userReactions[$postId] ?? null;
                            $myLikeReaction = ($myReaction && in_array($myReaction, $LIKE_FAMILY_TYPES, true)) ? $myReaction : null;
                            $triggerEmoji = $myLikeReaction ? $REACTION_ICONS[$myLikeReaction]['emoji'] : '👍';
                            ?>
                            <div class="reaction-bar" data-post-id="<?php echo $postId; ?>">
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <div class="reaction-picker-wrap">
                                        <button type="button" class="reaction-trigger<?php echo $myLikeReaction ? ' active' : ''; ?>" aria-label="React to this post">
                                            <span class="reaction-trigger-emoji"><?php echo $triggerEmoji; ?></span>
                                            <?php if ($likeTotal > 0): ?><span class="reaction-trigger-count"><?php echo $likeTotal; ?></span><?php endif; ?>
                                        </button>
                                        <div class="reaction-picker">
                                            <?php foreach ($LIKE_FAMILY_TYPES as $type): $r = $REACTION_ICONS[$type]; ?>
                                                <button type="button" class="reaction-option" data-post-id="<?php echo $postId; ?>" data-type="<?php echo $type; ?>" title="<?php echo htmlspecialchars($r['label']); ?>"><?php echo $r['emoji']; ?></button>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <button type="button" class="dislike-trigger<?php echo $myReaction === 'dislike' ? ' active' : ''; ?>" data-post-id="<?php echo $postId; ?>" aria-label="Dislike this post" title="Dislike">
                                        <span class="reaction-trigger-emoji">👎</span>
                                        <?php if ($dislikeCount > 0): ?><span class="dislike-trigger-count"><?php echo $dislikeCount; ?></span><?php endif; ?>
                                    </button>
                                <?php else: ?>
                                    <span class="reaction-trigger" style="cursor: default;" title="Login to react">
                                        <span class="reaction-trigger-emoji">👍</span>
                                        <?php if ($likeTotal > 0): ?><span class="reaction-trigger-count"><?php echo $likeTotal; ?></span><?php endif; ?>
                                    </span>
                                    <span class="dislike-trigger" style="cursor: default;" title="Login to react">
                                        <span class="reaction-trigger-emoji">👎</span>
                                        <?php if ($dislikeCount > 0): ?><span class="dislike-trigger-count"><?php echo $dislikeCount; ?></span><?php endif; ?>
                                    </span>
                                <?php endif; ?>

                                <?php $postComments = $comments[$postId] ?? []; ?>
                                <button type="button" class="comment-toggle" data-post-id="<?php echo $postId; ?>">
                                    💬 <span class="comment-toggle-count"><?php echo count($postComments); ?></span> Comment<?php echo count($postComments) === 1 ? '' : 's'; ?>
                                </button>
                            </div>

                            <div class="comment-section" data-post-id="<?php echo $postId; ?>" style="display: none;">
                                <div class="comment-list">
                                    <?php foreach ($postComments as $c): ?>
                                        <?php
                                        $cpic = $c['profile_pic'] ?? 'default.png';
                                        if ($cpic === 'default.png') {
                                            $cpic = default_avatar_for_seed($c['comment_id']);
                                        }
                                        $mine = isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)$c['user_id'];
                                        ?>
                                        <div class="comment-item" data-comment-id="<?php echo $c['comment_id']; ?>" data-raw="<?php echo htmlspecialchars($c['content']); ?>">
                                            <img src="uploads/avatars/<?php echo htmlspecialchars($cpic); ?>" alt="" class="comment-avatar">
                                            <div class="comment-body">
                                                <div class="comment-meta">
                                                    <span class="comment-author"><?php echo htmlspecialchars($c['fullname']); ?></span>
                                                    <span class="comment-time"><?php echo date("F j, Y, g:i a", strtotime($c['created_at'])); ?></span>
                                                </div>
                                                <p class="comment-text"><?php echo nl2br(htmlspecialchars($c['content'])); ?></p>
                                            </div>
                                            <?php if ($mine): ?>
                                                <div class="comment-actions">
                                                    <button type="button" class="comment-edit-btn" title="Edit comment">Edit</button>
                                                    <button type="button" class="comment-delete" title="Delete comment">Delete</button>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <form class="comment-form" data-post-id="<?php echo $postId; ?>">
                                        <input type="text" class="comment-input" maxlength="1000" placeholder="Write a comment..." required>
                                        <button type="submit" class="btn btn-primary comment-submit">Post</button>
                                    </form>
                                <?php else: ?>
                                    <p class="comment-login-hint"><a href="login.php">Login</a> to leave a comment.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="game-card" style="padding: 1.875rem; text-align: center;">
                    <h3 style="color: var(--muted);">No posts yet.</h3>
                    <p style="color: var(--muted);">Be the first to start a discussion!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
