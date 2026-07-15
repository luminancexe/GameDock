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
                const ty = my * d * 40 + sy * d * -60;
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
    if (showcase && !reduced) {
        const tracks = ["track1", "track2"]
            .map((id, i) => {
                const el = document.getElementById(id);
                return el ? { el: el, pos: 0, base: i === 0 ? -0.45 : 0.35, unit: 0 } : null;
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

            let mouseNorm = 0;
            window.addEventListener("mousemove", (e) => {
                mouseNorm = (e.clientX / window.innerWidth) * 2 - 1; // -1 (left edge) .. 1 (right edge)
            });

            let lastTime = null;
            let isVisible = true;
            const stepTrack = (track, dt) => {
                const speed = (track.base + mouseNorm * 0.85) * (dt / 16.67);
                track.pos -= speed;
                const unit = track.unit;
                if (unit > 0) {
                    if (track.pos <= -unit) track.pos += unit;
                    if (track.pos >= 0) track.pos -= unit;
                }
                track.el.style.transform = `translateX(${track.pos}px)`;
            };
            const tick = (t) => {
                if (!isVisible) return;
                if (lastTime === null) lastTime = t;
                const dt = Math.min(t - lastTime, 48);
                lastTime = t;
                tracks.forEach((track) => stepTrack(track, dt));
                requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);

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
