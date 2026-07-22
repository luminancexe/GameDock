// forum-comments.js — expandable comment threads per forum post, added via
// AJAX so the page never reloads. Mirrors the request/response style already
// used by forum-reactions.js ($.post to a JSON endpoint).

function renderComment(postId, c) {
    return (
        '<div class="comment-item bounce-in" data-comment-id="' + c.comment_id + '" data-raw="' + c.content + '">' +
            '<img src="uploads/avatars/' + c.avatar + '" alt="" class="comment-avatar">' +
            '<div class="comment-body">' +
                '<div class="comment-meta">' +
                    '<span class="comment-author">' + c.fullname + "</span>" +
                    '<span class="comment-time">' + c.created_at + "</span>" +
                "</div>" +
                '<p class="comment-text">' + c.content.replace(/\n/g, "<br>") + "</p>" +
            "</div>" +
            (c.mine
                ? '<div class="comment-actions">' +
                      '<button type="button" class="comment-edit-btn" title="Edit comment">Edit</button>' +
                      '<button type="button" class="comment-delete" title="Delete comment">Delete</button>' +
                  "</div>"
                : "") +
        "</div>"
    );
}

function updateCommentCount($bar, total) {
    $bar.find(".comment-toggle-count").text(total);
    $bar.find(".comment-toggle").contents().last()[0].textContent =
        " " + (total === 1 ? "Comment" : "Comments");
}

$(document).ready(function () {
    $(".comment-toggle").on("click", function () {
        const postId = $(this).data("post-id");
        $('.comment-section[data-post-id="' + postId + '"]').slideToggle(180);
    });

    $(".comment-form").on("submit", function (e) {
        e.preventDefault();
        const $form = $(this);
        const $input = $form.find(".comment-input");
        const content = $input.val().trim();
        if (!content) return;
        const postId = $form.data("post-id");

        $form.find(".comment-submit").prop("disabled", true);
        $.post("forum_comment_action.php", { post_id: postId, content: content })
            .done(function (res) {
                if (!res || !res.success) return;
                const $list = $('.comment-list').filter(function () {
                    return $(this).closest(".comment-section").data("post-id") == postId;
                });
                $list.append(renderComment(postId, res.comment));
                $input.val("");
                updateCommentCount($('.reaction-bar[data-post-id="' + postId + '"]'), res.total);
            })
            .fail(function (xhr) {
                if (xhr.status === 401) {
                    window.location.href = "login.php";
                } else {
                    alert((xhr.responseJSON && xhr.responseJSON.error) || "Could not post comment.");
                }
            })
            .always(function () {
                $form.find(".comment-submit").prop("disabled", false);
            });
    });

    $(document).on("click", ".comment-delete", function () {
        const $item = $(this).closest(".comment-item");
        const commentId = $item.data("comment-id");
        const postId = $item.closest(".comment-section").data("post-id");
        if (!confirm("Delete this comment?")) return;

        $.post("forum_comment_action.php", { action: "delete", comment_id: commentId })
            .done(function (res) {
                if (!res || !res.success) return;
                $item.fadeOut(150, function () {
                    $(this).remove();
                    const $bar = $('.reaction-bar[data-post-id="' + postId + '"]');
                    const newTotal = Math.max(0, parseInt($bar.find(".comment-toggle-count").text(), 10) - 1);
                    updateCommentCount($bar, newTotal);
                });
            })
            .fail(function (xhr) {
                alert((xhr.responseJSON && xhr.responseJSON.error) || "Could not delete comment.");
            });
    });

    // Edit — swaps the comment text for an inline form pre-filled with the
    // original (unescaped) content, read back out of data-raw so re-editing
    // never double-encodes entities.
    $(document).on("click", ".comment-edit-btn", function () {
        const $item = $(this).closest(".comment-item");
        const $body = $item.find(".comment-body");
        if ($body.find(".comment-edit-form").length) return;

        const raw = $item.data("raw");
        const $text = $body.find(".comment-text");

        const $input = $('<input type="text" class="comment-edit-input" maxlength="1000">').val(raw);
        const $save = $('<button type="submit" class="btn btn-primary comment-edit-save">Save</button>');
        const $cancel = $('<button type="button" class="btn comment-edit-cancel">Cancel</button>');
        const $actions = $('<div class="comment-edit-actions"></div>').append($save, $cancel);
        const $form = $('<form class="comment-edit-form"></form>').append($input, $actions);

        $text.hide();
        $body.append($form);
        $input.trigger("focus");
    });

    $(document).on("click", ".comment-edit-cancel", function () {
        const $form = $(this).closest(".comment-edit-form");
        $form.closest(".comment-body").find(".comment-text").show();
        $form.remove();
    });

    $(document).on("submit", ".comment-edit-form", function (e) {
        e.preventDefault();
        const $form = $(this);
        const $item = $form.closest(".comment-item");
        const newContent = $form.find(".comment-edit-input").val().trim();
        if (!newContent) return;
        const commentId = $item.data("comment-id");

        $form.find(".comment-edit-save").prop("disabled", true);
        $.post("forum_comment_action.php", { action: "edit", comment_id: commentId, content: newContent })
            .done(function (res) {
                if (!res || !res.success) return;
                $item.find(".comment-text").text(newContent).show();
                $item.attr("data-raw", newContent).data("raw", newContent);
                $form.remove();
            })
            .fail(function (xhr) {
                alert((xhr.responseJSON && xhr.responseJSON.error) || "Could not update comment.");
                $form.find(".comment-edit-save").prop("disabled", false);
            });
    });
});
