// cursor.js — theme-aware custom cursor: glow trail, a magnetic ring that reacts
// to interactive elements, and sparkle particles while moving. Colors come from
// the site's existing --accent/--rent CSS variables, so it re-themes automatically
// when the light/dark toggle flips data-theme on <html>. Only activates on
// fine-pointer devices and honours prefers-reduced-motion, matching the rest of
// the site's motion conventions (parallax, marquee, avatar builder bounce).
$(function () {
    const fine = window.matchMedia("(pointer: fine)").matches;
    const reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    if (!fine || reduced) return;

    const $body = $("body");
    $body.addClass("custom-cursor-active");
    $body.append(
        '<div class="gd-cursor-ring" id="gdCursorRing" aria-hidden="true"></div>' +
        '<div class="gd-cursor-trail" id="gdT3" aria-hidden="true"></div>' +
        '<div class="gd-cursor-trail" id="gdT2" aria-hidden="true"></div>' +
        '<div class="gd-cursor-trail" id="gdT1" aria-hidden="true"></div>' +
        '<div class="gd-cursor-dot" id="gdDot" aria-hidden="true"></div>'
    );

    const targets = ["gdDot", "gdT1", "gdT2", "gdT3", "gdCursorRing"].map((id) => document.getElementById(id));
    const factors = [0.35, 0.3, 0.22, 0.15, 0.18];
    let mx = window.innerWidth / 2;
    let my = window.innerHeight / 2;
    const pos = targets.map(() => ({ x: mx, y: my }));

    let lastX = null;
    let lastY = null;

    $(document).on("mousemove", function (e) {
        mx = e.clientX;
        my = e.clientY;

        const moved = lastX === null || Math.hypot(e.clientX - lastX, e.clientY - lastY) > 6;
        lastX = e.clientX;
        lastY = e.clientY;
        if (moved && Math.random() <= 0.5) {
            const size = 3 + Math.random() * 4;
            const $s = $('<div class="gd-cursor-sparkle" aria-hidden="true"></div>').css({
                left: e.clientX + "px",
                top: e.clientY + "px",
                width: size + "px",
                height: size + "px",
                background: Math.random() > 0.5 ? "var(--accent)" : "var(--rent)",
            });
            $body.append($s);
            requestAnimationFrame(() => {
                $s.css({
                    transform: "translate(" + (Math.random() - 0.5) * 36 + "px, " + ((Math.random() - 0.5) * 36 + 16) + "px) scale(0)",
                    opacity: 0,
                });
            });
            setTimeout(() => $s.remove(), 650);
        }
    });

    function tick() {
        let tx = mx;
        let ty = my;
        targets.forEach((el, i) => {
            const p = pos[i];
            const f = factors[i];
            p.x += (tx - p.x) * f;
            p.y += (ty - p.y) * f;
            const rect = el.getBoundingClientRect();
            el.style.transform = "translate(" + (p.x - rect.width / 2) + "px, " + (p.y - rect.height / 2) + "px)";
            tx = p.x;
            ty = p.y;
        });
        requestAnimationFrame(tick);
    }
    tick();

    const $ring = $("#gdCursorRing");
    const interactiveSelector = "a, button, input, select, textarea, [role='button'], .btn, .dropdown-toggle";
    $(document).on("mouseenter", interactiveSelector, function () {
        $ring.addClass("gd-ring-active");
    });
    $(document).on("mouseleave", interactiveSelector, function () {
        $ring.removeClass("gd-ring-active");
    });
});
