// avatar-builder.js — gender-filtered avatar gallery picker.
// Two buttons filter a bundled illustrated avatar pack (uploads/avatars/gallery/);
// clicking a thumbnail selects it, then Save persists the choice as profile_pic.

$(document).ready(function () {
    const $root = $("#avatarPicker");
    if (!$root.length) return;

    const galleryData = JSON.parse(document.getElementById("avatarGalleryData").textContent);
    const $grid = $("#avatarGalleryGrid");
    const $actions = $("#avatarPickerActions");
    const $saveBtn = $("#avatarSave");
    const $saveMsg = $("#avatarSaveMsg");
    const reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    let selectedFile = null;

    function renderGrid(gender) {
        $grid.empty();
        const files = galleryData[gender] || [];
        files.forEach((file, i) => {
            const $item = $(`
                <button type="button" class="avatar-gallery-item" data-file="${file}">
                    <img src="uploads/avatars/gallery/${file}" alt="Avatar option" loading="lazy">
                </button>
            `);
            if (!reduced) {
                $item.css("animation-delay", (i * 22) + "ms");
                $item.addClass("bounce-in");
            }
            $grid.append($item);
        });
        $grid.addClass("show");
        $actions.show();
        selectedFile = null;
        $saveBtn.prop("disabled", true);
    }

    $root.on("click", ".avatar-gender-btn", function () {
        $(".avatar-gender-btn").removeClass("active");
        $(this).addClass("active").addClass("bounce-pop");
        setTimeout(() => $(this).removeClass("bounce-pop"), 400);
        renderGrid($(this).data("gender"));
    });

    $grid.on("click", ".avatar-gallery-item", function () {
        $(".avatar-gallery-item").removeClass("selected");
        $(this).addClass("selected");
        if (!reduced) {
            $(this).removeClass("bounce-pop");
            void this.offsetWidth;
            $(this).addClass("bounce-pop");
        }
        selectedFile = $(this).data("file");
        $saveBtn.prop("disabled", false);
    });

    $saveBtn.on("click", function () {
        if (!selectedFile) return;
        const $btn = $(this);
        $btn.prop("disabled", true).text("Saving...");
        $.post("avatar_action.php", { gallery_avatar: selectedFile })
            .done(function () {
                $saveMsg.text("Saved! Redirecting to your profile...").addClass("show");
                setTimeout(() => { window.location.href = "profile.php"; }, 900);
            })
            .fail(function () {
                $saveMsg.text("Something went wrong — please try again.").addClass("show");
                $btn.prop("disabled", false).text("Save Avatar");
            });
    });
});
