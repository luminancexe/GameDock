// forum-reactions.js — Facebook-style reaction trigger: a quick click/tap reacts
// instantly with "like"; holding the button for a moment reveals the full emoji
// picker instead. Uses Pointer Events so the same code drives mouse and touch.

const RX_META = {
    like: { emoji: "👍", label: "Like" },
    love: { emoji: "❤️", label: "Love" },
    haha: { emoji: "😆", label: "Haha" },
    wow: { emoji: "😮", label: "Wow" },
    sad: { emoji: "😢", label: "Sad" },
    angry: { emoji: "😠", label: "Angry" },
};
const LIKE_FAMILY = Object.keys(RX_META);
const HOLD_MS = 380;
const MOVE_CANCEL_PX = 10;

function setCount($el, selector, tagClass, value) {
    const $count = $el.find(selector);
    if (value > 0) {
        if ($count.length) {
            $count.text(value);
        } else {
            $el.append('<span class="' + tagClass + '">' + value + "</span>");
        }
    } else {
        $count.remove();
    }
}

function applyReactionResult($bar, res) {
    // The "like" trigger only ever reflects the six positive reactions —
    // dislike is a separate, always-visible button with its own state.
    const $trigger = $bar.find(".reaction-trigger");
    const isLikeFamily = res.userReaction && LIKE_FAMILY.indexOf(res.userReaction) !== -1;
    const emoji = isLikeFamily ? RX_META[res.userReaction].emoji : "👍";
    $trigger.find(".reaction-trigger-emoji").text(emoji);
    $trigger.toggleClass("active", !!isLikeFamily);

    let likeTotal = 0;
    LIKE_FAMILY.forEach(function (t) { likeTotal += res.counts[t] || 0; });
    setCount($trigger, ".reaction-trigger-count", "reaction-trigger-count", likeTotal);

    const $dislike = $bar.find(".dislike-trigger");
    $dislike.toggleClass("active", res.userReaction === "dislike");
    setCount($dislike, ".dislike-trigger-count", "dislike-trigger-count", res.counts.dislike || 0);
}

function sendReaction(postId, type, $originEl) {
    if ($originEl) {
        $originEl.addClass("rx-pop");
        setTimeout(() => $originEl.removeClass("rx-pop"), 350);
    }
    $.post("forum_reaction_action.php", { post_id: postId, reaction_type: type })
        .done(function (res) {
            if (!res || !res.success) return;
            const $bar = $('.reaction-bar[data-post-id="' + postId + '"]');
            applyReactionResult($bar, res);
            $bar.find(".reaction-picker-wrap").removeClass("open");
        })
        .fail(function (xhr) {
            if (xhr.status === 401) window.location.href = "login.php";
        });
}

$(document).ready(function () {
    let holdTimer = null;
    let holdTriggered = false;
    let startX = 0;
    let startY = 0;

    $(".reaction-trigger").on("pointerdown", function (e) {
        if (e.button !== undefined && e.button > 0) return;
        const $wrap = $(this).closest(".reaction-picker-wrap");
        if (!$wrap.length) return;
        holdTriggered = false;
        startX = e.clientX;
        startY = e.clientY;
        clearTimeout(holdTimer);
        holdTimer = setTimeout(function () {
            holdTriggered = true;
            $(".reaction-picker-wrap.open").not($wrap).removeClass("open");
            $wrap.addClass("open");
        }, HOLD_MS);
    });

    $(".reaction-trigger").on("pointermove", function (e) {
        if (Math.hypot(e.clientX - startX, e.clientY - startY) > MOVE_CANCEL_PX) {
            clearTimeout(holdTimer);
        }
    });

    $(".reaction-trigger").on("pointerup pointerleave pointercancel", function () {
        clearTimeout(holdTimer);
    });

    $(".reaction-trigger").on("click", function (e) {
        e.preventDefault();
        const $wrap = $(this).closest(".reaction-picker-wrap");
        if (!$wrap.length) return;

        if (holdTriggered) {
            // The hold already opened the picker — let the user pick from it,
            // this trailing click shouldn't also fire a quick "like".
            holdTriggered = false;
            return;
        }

        const postId = $(this).closest(".reaction-bar").data("post-id");
        sendReaction(postId, "like", null);
    });

    $(document).on("click", function (e) {
        if (!$(e.target).closest(".reaction-picker-wrap").length) {
            $(".reaction-picker-wrap.open").removeClass("open");
        }
    });

    $(".reaction-option").on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        const $btn = $(this);
        sendReaction($btn.data("post-id"), $btn.data("type"), $btn);
    });

    // Thumbs-down is a plain toggle button — no hold-to-open picker, it only
    // ever sends "dislike" and clicking it again removes it (same toggle-off
    // behavior forum_reaction_action.php already applies to every reaction type).
    $("button.dislike-trigger").on("click", function (e) {
        e.preventDefault();
        const $btn = $(this);
        sendReaction($btn.data("post-id"), "dislike", $btn);
    });
});
