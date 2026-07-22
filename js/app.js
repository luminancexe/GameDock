// app.js
$(document).ready(function() {
    // Basic confirmation popup for actions
    $(".confirm-action").on("click", function(e) {
        if(!confirm("Are you sure you want to perform this action?")) {
            e.preventDefault();
        }
    });

    // Cookie consent banner — either choice dismisses it (header.php checks
    // just for the cookie's presence server-side, so it won't flash back on
    // reload either way), but the two buttons aren't the same action wearing
    // different labels: accepting remembers that for a full year, while
    // declining is only remembered for a week, so people who said no get
    // asked again periodically instead of being silently opted in forever.
    const $cookieBanner = $("#cookieBanner");
    function dismissCookieBanner(value, maxAgeSeconds) {
        document.cookie = "cookie_consent=" + value + "; path=/; max-age=" + maxAgeSeconds + "; samesite=Lax";
        $cookieBanner.addClass("hide");
        setTimeout(() => $cookieBanner.remove(), 350);
    }
    $("#cookieAccept").on("click", function() {
        dismissCookieBanner("accepted", 60 * 60 * 24 * 365);
    });
    $("#cookieDecline").on("click", function() {
        dismissCookieBanner("declined", 60 * 60 * 24 * 7);
    });

    // Mobile nav toggle
    const $navbar = $("#navbar");
    const $navToggle = $("#navToggle");
    if ($navbar.length && $navToggle.length) {
        $navToggle.on("click", function() {
            $navbar.toggleClass("open");
            const isOpen = $navbar.hasClass("open");
            $(this).attr("aria-expanded", isOpen ? "true" : "false");
        });
    }

    // Light/dark theme toggle — header.php already renders the correct data-theme
    // server-side from the "theme" cookie, so there's no flash-of-wrong-theme to
    // guard against here; this just wires up the click, writes the cookie, and
    // keeps aria in sync.
    const $themeToggle = $("#themeToggle");
    if ($themeToggle.length) {
        const syncLabel = () => {
            const isLight = document.documentElement.getAttribute("data-theme") === "light";
            $themeToggle.attr("aria-pressed", isLight ? "true" : "false");
            $themeToggle.attr("aria-label", isLight ? "Switch to dark theme" : "Switch to light theme");
        };
        syncLabel();
        $themeToggle.on("click", function() {
            const isLight = document.documentElement.getAttribute("data-theme") === "light";
            const maxAge = 60 * 60 * 24 * 365; // 1 year
            if (isLight) {
                document.documentElement.removeAttribute("data-theme");
                document.cookie = "theme=dark; path=/; max-age=" + maxAge + "; samesite=Lax";
            } else {
                document.documentElement.setAttribute("data-theme", "light");
                document.cookie = "theme=light; path=/; max-age=" + maxAge + "; samesite=Lax";
            }
            syncLabel();
        });
    }

    // Rentals nav dropdown — click/tap toggles (desktop also gets it free via CSS :hover)
    $(".dropdown").each(function() {
        const $dropdown = $(this);
        const $toggle = $dropdown.find(".dropdown-toggle");
        if (!$toggle.length) return;
        
        $toggle.on("click", function(e) {
            e.preventDefault();
            $dropdown.toggleClass("open");
            const isOpen = $dropdown.hasClass("open");
            $(this).attr("aria-expanded", isOpen ? "true" : "false");
        });
    });

    $(document).on("click", function(e) {
        $(".dropdown.open").each(function() {
            if (!$.contains(this, e.target)) {
                $(this).removeClass("open");
                $(this).find(".dropdown-toggle").attr("aria-expanded", "false");
            }
        });
    });

    // Hero parallax (mouse + scroll) — decorative only, skipped under reduced motion
    const $field = $("#heroField");
    const reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    if ($field.length && !reduced) {
        const $layers = $field.find("[data-depth]");
        let mx = 0, my = 0, sy = 0, ticking = false;

        const apply = () => {
            $layers.each(function() {
                const $el = $(this);
                const d = parseFloat($el.attr("data-depth"));
                const tx = mx * d * 40;
                const ty = my * d * 40 + sy * d * -2;
                $el.css("transform", ($el.data("baseTransform") || "") + ` translate3d(${tx}px, ${ty}px, 0)`);
            });
            ticking = false;
        };
        const request = () => {
            if (!ticking) { requestAnimationFrame(apply); ticking = true; }
        };

        $layers.each(function() { 
            $(this).data("baseTransform", $(this).css("transform") === "none" ? "" : $(this).css("transform")); 
        });

        $(window).on("mousemove", function(e) {
            mx = (e.clientX / window.innerWidth) - 0.5;
            my = (e.clientY / window.innerHeight) - 0.5;
            request();
        });
        
        $(window).on("scroll", function() {
            sy = window.scrollY;
            request();
        });
    }

    // Game library showcase
    const $showcase = $("#showcase");
    if ($showcase.length) {
        $showcase.on("mouseover", function(e) {
            const $thumb = $(e.target).closest(".thumb");
            if (!$thumb.length) return;
            $thumb.closest(".marquee-row").addClass("row-hover");
            $showcase.addClass("showcase-hover");
        });
        $showcase.on("mouseout", function(e) {
            const $thumb = $(e.target).closest(".thumb");
            if (!$thumb.length) return;
            if (e.relatedTarget && $.contains($thumb[0], e.relatedTarget)) return;
            $thumb.closest(".marquee-row").removeClass("row-hover");
            $showcase.removeClass("showcase-hover");
        });
    }
    
    if ($showcase.length && !reduced) {
        const tracks = ["track1", "track2"]
            .map((id, i) => {
                const $el = $("#" + id);
                return $el.length ? { el: $el[0], $el: $el, pos: 0, base: i === 0 ? -0.45 : 0.35, unit: 0, dragging: false } : null;
            })
            .filter(Boolean);

        const fillTrack = (track) => {
            const $firstGroup = track.$el.children().first();
            if (!$firstGroup.length) return;
            track.unit = $firstGroup[0].getBoundingClientRect().width || track.unit;
            if (!track.unit) return;
            let guard = 0;
            while (track.el.scrollWidth < window.innerWidth + track.unit && guard < 20) {
                const $clone = $firstGroup.clone(true);
                $clone.attr("aria-hidden", "true");
                $clone.find("a").attr("tabindex", "-1");
                track.$el.append($clone);
                guard++;
            }
        };

        if (tracks.length) {
            tracks.forEach(fillTrack);
            $(window).on("load resize", () => tracks.forEach(fillTrack));

            let targetMouseNorm = 0;
            let currentMouseNorm = 0;
            $(window).on("mousemove", function(e) {
                targetMouseNorm = (e.clientX / window.innerWidth) * 2 - 1;
            });

            const wrapPos = (track) => {
                const unit = track.unit;
                if (unit <= 0) return;
                while (track.pos <= -unit) track.pos += unit;
                while (track.pos >= 0) track.pos -= unit;
            };

            let lastTime = null;
            let isVisible = true;
            const stepTrack = (track, dt) => {
                if (track.dragging) return;
                const speed = (track.base + currentMouseNorm * 0.85) * (dt / 16.67);
                track.pos -= speed;
                wrapPos(track);
                track.$el.css("transform", `translateX(${track.pos}px)`);
            };
            const tick = (t) => {
                if (!isVisible) return;
                if (lastTime === null) lastTime = t;
                const dt = Math.min(t - lastTime, 48);
                lastTime = t;
                
                currentMouseNorm += (targetMouseNorm - currentMouseNorm) * 0.05 * (dt / 16.67);
                
                tracks.forEach((track) => stepTrack(track, dt));
                requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);

            const DRAG_START_THRESHOLD = 6;
            tracks.forEach((track) => {
                let pointerDown = false;
                let pointerId = null;
                let startX = 0;
                let startPos = 0;
                let suppressClick = false;

                track.$el.on("dragstart", (e) => e.preventDefault());

                track.$el.on("pointerdown", (e) => {
                    if (e.originalEvent.button !== undefined && e.originalEvent.button !== 0) return;
                    pointerDown = true;
                    pointerId = e.originalEvent.pointerId;
                    startX = e.clientX;
                    startPos = track.pos;
                    suppressClick = false;
                });
                track.$el.on("pointermove", (e) => {
                    if (!pointerDown) return;
                    const delta = e.clientX - startX;
                    if (!track.dragging) {
                        if (Math.abs(delta) < DRAG_START_THRESHOLD) return;
                        track.dragging = true;
                        suppressClick = true;
                        track.$el.addClass("dragging");
                        track.el.setPointerCapture(pointerId);
                    }
                    track.pos = startPos + delta;
                    wrapPos(track);
                    track.$el.css("transform", `translateX(${track.pos}px)`);
                });
                const endDrag = () => {
                    pointerDown = false;
                    if (!track.dragging) return;
                    track.dragging = false;
                    track.$el.removeClass("dragging");
                };
                track.$el.on("pointerup pointercancel", endDrag);
                track.el.addEventListener("click", (e) => {
                    if (suppressClick) {
                        e.preventDefault();
                        e.stopPropagation();
                        suppressClick = false;
                    }
                }, true);
            });

            if ("IntersectionObserver" in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        isVisible = entry.isIntersecting;
                        if (isVisible) {
                            lastTime = null;
                            requestAnimationFrame(tick);
                        }
                    });
                });
                observer.observe($showcase[0]);
            }
        }
    }
});
