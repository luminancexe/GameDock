// app.js
document.addEventListener("DOMContentLoaded", () => {
    // Basic confirmation popup for actions
    const confirmActions = document.querySelectorAll(".confirm-action");
    confirmActions.forEach(el => {
        el.addEventListener("click", (e) => {
            if(!confirm("Are you sure you want to perform this action?")) {
                e.preventDefault();
            }
        });
    });

    // Mobile nav toggle
    const navbar = document.getElementById("navbar");
    const navToggle = document.getElementById("navToggle");
    if (navbar && navToggle) {
        navToggle.addEventListener("click", () => {
            const isOpen = navbar.classList.toggle("open");
            navToggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
        });
    }

    // Rentals nav dropdown — click/tap toggles (desktop also gets it free via CSS :hover)
    document.querySelectorAll(".dropdown").forEach((dropdown) => {
        const toggle = dropdown.querySelector(".dropdown-toggle");
        if (!toggle) return;
        toggle.addEventListener("click", (e) => {
            e.preventDefault();
            const isOpen = dropdown.classList.toggle("open");
            toggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
        });
    });
    document.addEventListener("click", (e) => {
        document.querySelectorAll(".dropdown.open").forEach((dropdown) => {
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove("open");
                const toggle = dropdown.querySelector(".dropdown-toggle");
                if (toggle) toggle.setAttribute("aria-expanded", "false");
            }
        });
    });

    // Hero parallax (mouse + scroll) — decorative only, skipped under reduced motion
    const field = document.getElementById("heroField");
    const reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    if (field && !reduced) {
        const layers = Array.prototype.slice.call(field.querySelectorAll("[data-depth]"));
        let mx = 0, my = 0, sy = 0, ticking = false;

        const apply = () => {
            layers.forEach(el => {
                const d = parseFloat(el.getAttribute("data-depth"));
                const tx = mx * d * 40;
                const ty = my * d * 40 + sy * d * -2;
                el.style.transform = (el.dataset.baseTransform || "") + ` translate3d(${tx}px, ${ty}px, 0)`;
            });
            ticking = false;
        };
        const request = () => {
            if (!ticking) { requestAnimationFrame(apply); ticking = true; }
        };

        layers.forEach(el => { el.dataset.baseTransform = el.style.transform || ""; });

        window.addEventListener("mousemove", (e) => {
            mx = (e.clientX / window.innerWidth) - 0.5;
            my = (e.clientY / window.innerHeight) - 0.5;
            request();
        });
        window.addEventListener("scroll", () => {
            sy = window.scrollY;
            request();
        }, { passive: true });
    }

    // Game library showcase: two rows drift on their own, mouse position steers speed/direction
    const showcase = document.getElementById("showcase");
    if (showcase) {
        // JS-driven hover elevation (works even in browsers without :has() support) —
        // without this, a hovered thumb's scale-up can get painted over by the next
        // row or the footer, since those come later in the DOM.
        showcase.addEventListener("mouseover", (e) => {
            const thumb = e.target.closest(".thumb");
            if (!thumb) return;
            const row = thumb.closest(".marquee-row");
            if (row) row.classList.add("row-hover");
            showcase.classList.add("showcase-hover");
        });
        showcase.addEventListener("mouseout", (e) => {
            const thumb = e.target.closest(".thumb");
            if (!thumb) return;
            if (e.relatedTarget && thumb.contains(e.relatedTarget)) return;
            const row = thumb.closest(".marquee-row");
            if (row) row.classList.remove("row-hover");
            showcase.classList.remove("showcase-hover");
        });
    }
    if (showcase && !reduced) {
        const tracks = ["track1", "track2"]
            .map((id, i) => {
                const el = document.getElementById(id);
                return el ? { el: el, pos: 0, base: i === 0 ? -0.45 : 0.35, unit: 0, dragging: false } : null;
            })
            .filter(Boolean);

        // Make sure each row has enough duplicated copies to outspan the viewport —
        // on very wide/zoomed-out screens two copies alone would visibly run out.
        const fillTrack = (track) => {
            const firstGroup = track.el.children[0];
            if (!firstGroup) return;
            track.unit = firstGroup.getBoundingClientRect().width || track.unit;
            if (!track.unit) return;
            let guard = 0;
            while (track.el.scrollWidth < window.innerWidth + track.unit && guard < 20) {
                const clone = firstGroup.cloneNode(true);
                clone.setAttribute("aria-hidden", "true");
                clone.querySelectorAll("a").forEach((a) => a.setAttribute("tabindex", "-1"));
                track.el.appendChild(clone);
                guard++;
            }
        };

        if (tracks.length) {
            tracks.forEach(fillTrack);
            window.addEventListener("load", () => tracks.forEach(fillTrack));
            window.addEventListener("resize", () => tracks.forEach(fillTrack));

            let targetMouseNorm = 0;
            let currentMouseNorm = 0;
            window.addEventListener("mousemove", (e) => {
                targetMouseNorm = (e.clientX / window.innerWidth) * 2 - 1; // -1 (left edge) .. 1 (right edge)
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
                if (track.dragging) return; // manual drag owns the position for now
                const speed = (track.base + currentMouseNorm * 0.85) * (dt / 16.67);
                track.pos -= speed;
                wrapPos(track);
                track.el.style.transform = `translateX(${track.pos}px)`;
            };
            const tick = (t) => {
                if (!isVisible) return;
                if (lastTime === null) lastTime = t;
                const dt = Math.min(t - lastTime, 48);
                lastTime = t;
                
                // Lerp (smooth) the mouse input so changes in speed aren't harsh
                currentMouseNorm += (targetMouseNorm - currentMouseNorm) * 0.05 * (dt / 16.67);
                
                tracks.forEach((track) => stepTrack(track, dt));
                requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);

            // Click-and-drag scrolling: press and hold on a row, drag left/right to
            // scroll it manually. A plain click (no real drag) still opens the game.
            //
            // Important: pointer capture must NOT be taken on every pointerdown.
            // Once an element captures the pointer, browsers retarget the resulting
            // click event to the capturing element instead of the actual <a> under
            // the cursor, which silently kills the link's default navigation — for
            // every click, not just ones after a drag. So capture (and dragging)
            // only start once real movement crosses the threshold; a plain click
            // never engages capture at all, leaving native navigation untouched.
            const DRAG_START_THRESHOLD = 6;
            tracks.forEach((track) => {
                let pointerDown = false;
                let pointerId = null;
                let startX = 0;
                let startPos = 0;
                let suppressClick = false;

                track.el.addEventListener("dragstart", (e) => e.preventDefault());

                track.el.addEventListener("pointerdown", (e) => {
                    if (e.button !== undefined && e.button !== 0) return;
                    pointerDown = true;
                    pointerId = e.pointerId;
                    startX = e.clientX;
                    startPos = track.pos;
                    suppressClick = false;
                });
                track.el.addEventListener("pointermove", (e) => {
                    if (!pointerDown) return;
                    const delta = e.clientX - startX;
                    if (!track.dragging) {
                        if (Math.abs(delta) < DRAG_START_THRESHOLD) return;
                        // crossed the threshold — promote this gesture to a real drag
                        track.dragging = true;
                        suppressClick = true;
                        track.el.classList.add("dragging");
                        track.el.setPointerCapture(pointerId);
                    }
                    track.pos = startPos + delta;
                    wrapPos(track);
                    track.el.style.transform = `translateX(${track.pos}px)`;
                });
                const endDrag = () => {
                    pointerDown = false;
                    if (!track.dragging) return;
                    track.dragging = false;
                    track.el.classList.remove("dragging");
                };
                track.el.addEventListener("pointerup", endDrag);
                track.el.addEventListener("pointercancel", endDrag);
                track.el.addEventListener("click", (e) => {
                    if (suppressClick) {
                        e.preventDefault();
                        e.stopPropagation();
                        suppressClick = false;
                    }
                }, true);
            });

            // Pause the animation loop entirely while scrolled away from it —
            // no point burning CPU/GPU on transforms nobody can see.
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
                observer.observe(showcase);
            }
        }
    }
});
