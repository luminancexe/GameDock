<?php
require_once "includes/connection.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $avatar_file = "";

    // Check if custom upload
    if (isset($_FILES['avatar_upload']) && $_FILES['avatar_upload']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/avatars/';
        $tmp_name = $_FILES['avatar_upload']['tmp_name'];
        $name = basename($_FILES['avatar_upload']['name']);
        
        // Ensure it's an image
        $check = getimagesize($tmp_name);
        if ($check !== false) {
            $new_filename = "user_" . $user_id . "_" . time() . "_" . $name;
            if (move_uploaded_file($tmp_name, $upload_dir . $new_filename)) {
                $avatar_file = $new_filename;
            }
        }
    } 
    // Check if preset selected
    elseif (isset($_POST['preset_avatar']) && !empty($_POST['preset_avatar'])) {
        $preset = $_POST['preset_avatar'];
        $allowed_presets = ['default.png', 'ninja.png', 'retro.png'];
        if (in_array($preset, $allowed_presets)) {
            $avatar_file = $preset;
        }
    }

    if (!empty($avatar_file)) {
        $stmt = $pdo->prepare("UPDATE users SET profile_pic = ? WHERE user_id = ?");
        $stmt->execute([$avatar_file, $user_id]);
    }
}

header("Location: profile.php");
exit;
?>
