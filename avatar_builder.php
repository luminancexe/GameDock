<?php
require_once "includes/connection.php";
require_once "includes/auth_check.php";
require_once "includes/avatar_gallery_data.php";
requireLogin();

include "includes/header.php";
?>

<div class="container mt-4">
    <h2>Choose Your Avatar</h2>
    <p style="color: var(--muted); margin-bottom: 1.25rem;">Pick Male or Female to browse the gallery, then click an avatar to select it.</p>

    <div class="avatar-gallery-picker" id="avatarPicker">
        <div class="avatar-gender-row">
            <button type="button" class="avatar-gender-btn" data-gender="male">
                <span class="avatar-gender-icon">&#9794;</span> Male
            </button>
            <button type="button" class="avatar-gender-btn" data-gender="female">
                <span class="avatar-gender-icon">&#9792;</span> Female
            </button>
        </div>

        <div id="avatarGalleryGrid" class="avatar-gallery-grid"></div>

        <div class="avatar-preview-actions" id="avatarPickerActions" style="display: none;">
            <button type="button" id="avatarSave" class="btn" style="background-color: var(--rent); color: #04191b;" disabled>Save Avatar</button>
            <div id="avatarSaveMsg" class="avatar-save-msg"></div>
        </div>
    </div>

    <script id="avatarGalleryData" type="application/json"><?php echo json_encode($AVATAR_GALLERY); ?></script>

    <p class="mt-4"><a href="profile.php">&larr; Back to Profile</a></p>
</div>

<?php include "includes/footer.php"; ?>
