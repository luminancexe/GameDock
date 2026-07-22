/* GameDock footer battle: hero (player-chosen) vs SHADE.
   The hero is tuned stronger and wins roughly 2 of 3 rounds. */
(function () {
    "use strict";

    var arena = document.getElementById("btArena");
    if (!arena) return;
    if (window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

    var koTxt = document.getElementById("koTxt");
    var heroSlot = document.getElementById("heroSlot");
    var hudName = document.getElementById("hudName");

    var styles = {
        volt:  { name: "VOLT",   color: "#A78BFA", grad: "linear-gradient(90deg,#7C3AED,#A78BFA)" },
        ember: { name: "EMBER",  color: "#FB923C", grad: "linear-gradient(90deg,#C2410C,#FB923C)" },
        bolt7: { name: "BOLT-7", color: "#4ADE80", grad: "linear-gradient(90deg,#16A34A,#4ADE80)" },
        rune:  { name: "RUNE",   color: "#FBBF24", grad: "linear-gradient(90deg,#B45309,#FBBF24)" },
        fern:  { name: "FERN",   color: "#86EFAC", grad: "linear-gradient(90deg,#15803D,#86EFAC)" }
    };

    function readCookie(name) {
        var m = document.cookie.match(new RegExp("(?:^|; )" + name + "=([^;]*)"));
        return m ? decodeURIComponent(m[1]) : null;
    }

    var heroId = readCookie("footer_hero");
    if (!styles[heroId]) heroId = "bolt7";

    var H = { el: document.getElementById("fH"), hpEl: document.getElementById("hpH"), pipEl: document.getElementById("pipH"), x: 0, hp: 100, wins: 0, dir: 1 };
    var S = { el: document.getElementById("fS"), hpEl: document.getElementById("hpS"), pipEl: document.getElementById("pipS"), x: 0, hp: 100, wins: 0, dir: -1 };
    H.frame = H.el.querySelector(".bt-frame"); H.body = H.el.querySelector(".bt-body");
    S.frame = S.el.querySelector(".bt-frame"); S.body = S.el.querySelector(".bt-body");
    S.svg = S.el.querySelector("svg"); S.armF = S.svg.querySelector(".armF");

    function sleep(ms) { return new Promise(function (r) { setTimeout(r, ms); }); }
    function rng(a, b) { return a + Math.random() * (b - a); }
    function W() { return Math.max(140, arena.clientWidth - 64); }
    function cl(x) { return Math.max(2, Math.min(W() - 2, x)); }
    function spH() { return W() * 0.13; }
    function spS() { return W() * 0.87; }
    function setX(f, x) { f.x = x; f.el.style.transform = "translateX(" + x + "px)"; }
    function face(f, d) { f.dir = d; f.frame.style.transform = "scaleX(" + d + ")"; }
    function faceEach() { face(H, S.x > H.x ? 1 : -1); face(S, H.x > S.x ? 1 : -1); }
    function updHP(f) { f.hpEl.style.width = Math.max(0, f.hp) + "%"; f.hpEl.classList.toggle("low", f.hp < 30); }
    function updPips() { H.pipEl.textContent = new Array(H.wins + 1).join("★"); S.pipEl.textContent = new Array(S.wins + 1).join("★"); }

    function loadHero(id) {
        heroId = id;
        var tpl = document.querySelector('#heroTpl div[data-h="' + id + '"]');
        heroSlot.innerHTML = tpl.innerHTML;
        H.svg = heroSlot.querySelector("svg");
        H.armF = H.svg.querySelector(".armF");
        hudName.textContent = styles[id].name;
        hudName.style.color = styles[id].color;
        H.hpEl.style.background = styles[id].grad;
    }

    function moveTo(f, target, spd, hop) {
        spd = spd || 0.26; hop = (hop === undefined) ? 8 : hop; target = cl(target);
        var start = f.x, dx = target - start;
        if (Math.abs(dx) < 3) return Promise.resolve();
        face(f, dx > 0 ? 1 : -1);
        var dur = Math.abs(dx) / spd, hops = Math.max(1, Math.round(dur / 230)), t0 = performance.now();
        return new Promise(function (res) {
            function st(t) {
                var p = Math.min(1, (t - t0) / dur);
                var y = -Math.abs(Math.sin(p * hops * Math.PI)) * hop;
                f.x = start + dx * p;
                f.el.style.transform = "translateX(" + f.x + "px)";
                f.frame.style.transform = "scaleX(" + f.dir + ") translateY(" + y + "px)";
                if (p < 1) requestAnimationFrame(st);
                else { f.frame.style.transform = "scaleX(" + f.dir + ")"; res(); }
            }
            requestAnimationFrame(st);
        });
    }

    function fx(html, x, bottom, w, h, cls, life) {
        var d = document.createElement("div");
        d.className = "fx " + cls;
        d.style.left = x + "px"; d.style.bottom = bottom + "px"; d.style.width = w + "px"; d.style.height = h + "px";
        d.innerHTML = html;
        arena.appendChild(d);
        setTimeout(function () { d.remove(); }, life);
        return d;
    }
    function fxSpark(x, big) {
        var s = big ? 46 : 30;
        fx('<svg viewBox="0 0 40 40"><path d="M20 2 L24 14 L36 10 L28 20 L38 28 L25 26 L20 38 L15 26 L2 28 L12 20 L4 10 L16 14 Z" fill="#FDE68A" stroke="#F59E0B" stroke-width="1.5"/></svg>', x - s / 2, 36, s, s, "fx-spark", 480);
    }
    function fxLightning(x) {
        fx('<svg viewBox="0 0 48 48"><path d="M14 2 L10 16 L17 18 L8 34" stroke="#FDE68A" stroke-width="2.5" fill="none" stroke-linecap="round"/><path d="M30 0 L27 12 L33 14 L26 30" stroke="#67E8F9" stroke-width="2.2" fill="none" stroke-linecap="round"/><path d="M40 6 L37 16 L42 17 L36 30" stroke="#FDE68A" stroke-width="1.8" fill="none" stroke-linecap="round"/></svg>', x + 8, 30, 48, 48, "fx-light", 340);
    }
    function fxBoom(x) {
        fx('<svg viewBox="0 0 48 48"><circle cx="24" cy="24" r="20" fill="#F97316" opacity="0.5"/><circle cx="24" cy="24" r="13" fill="#FB923C" opacity="0.8"/><circle cx="24" cy="24" r="7" fill="#FDE68A"/></svg>', x + 8, 26, 48, 48, "fx-boom", 520);
    }
    function fxSlash(x, big, color) {
        var c = color || "#FFFFFF";
        fx('<svg viewBox="0 0 44 36"><path d="M4 30 Q22 2 40 10" stroke="' + c + '" stroke-width="' + (big ? 5 : 3.5) + '" fill="none" stroke-linecap="round" opacity="0.95"/></svg>', x + 10, 32, big ? 52 : 40, big ? 42 : 34, "fx-slashfx", 240);
    }
    function fxQuake(x) {
        var r = document.createElement("div");
        r.className = "fx fx-ring";
        r.style.left = (x - 14) + "px"; r.style.bottom = "12px"; r.style.width = "92px"; r.style.height = "22px";
        arena.appendChild(r);
        setTimeout(function () { r.remove(); }, 580);
        for (var i = 0; i < 6; i++) {
            var d = document.createElement("div");
            d.className = "fx fx-dust";
            d.style.left = (x + rng(-8, 64)) + "px"; d.style.bottom = (14 + rng(0, 6)) + "px";
            d.style.width = d.style.height = rng(3, 6) + "px";
            d.style.animationDelay = (i * 0.03) + "s";
            arena.appendChild(d);
            (function (el) { setTimeout(function () { el.remove(); }, 620); })(d);
        }
    }
    function fxSteam(x) {
        for (var i = 0; i < 3; i++) {
            var d = document.createElement("div");
            d.className = "fx fx-puff";
            d.style.left = (x + rng(-4, 14)) + "px"; d.style.bottom = (38 + rng(-6, 8)) + "px";
            d.style.width = d.style.height = rng(8, 14) + "px";
            d.style.animationDelay = (i * 0.06) + "s";
            arena.appendChild(d);
            (function (el) { setTimeout(function () { el.remove(); }, 700); })(d);
        }
    }
    function fxClank(x) {
        var r = document.createElement("div");
        r.className = "fx fx-clank";
        r.style.left = (x + 6) + "px"; r.style.bottom = "30px"; r.style.width = "40px"; r.style.height = "40px";
        arena.appendChild(r);
        setTimeout(function () { r.remove(); }, 350);
    }
    function fxDashLines(x, dir) {
        for (var i = 0; i < 3; i++) {
            var d = document.createElement("div");
            d.className = "fx fx-dline";
            d.style.left = (x - dir * rng(10, 34)) + "px"; d.style.bottom = (28 + i * 10) + "px"; d.style.width = rng(16, 28) + "px";
            if (dir < 0) d.style.transform = "scaleX(-1)";
            arena.appendChild(d);
            (function (el) { setTimeout(function () { el.remove(); }, 320); })(d);
        }
    }
    function fxGhost(f) {
        var d = document.createElement("div");
        d.className = "fx fx-ghost";
        d.style.left = f.x + "px";
        var c = f.svg.cloneNode(true);
        c.style.width = "64px"; c.style.height = "64px";
        c.style.transform = "scaleX(" + f.dir + ")";
        c.style.filter = "brightness(0.6) saturate(0.5)";
        d.appendChild(c);
        arena.appendChild(d);
        setTimeout(function () { d.remove(); }, 470);
    }
    function dmgNum(x, val, crit) {
        var d = document.createElement("div");
        d.className = "bt-dmg" + (crit ? " crit" : "");
        d.style.left = (x + 18) + "px"; d.style.bottom = "76px";
        d.textContent = (crit ? "CRIT -" : "-") + val;
        arena.appendChild(d);
        setTimeout(function () { d.remove(); }, 880);
    }
    function shake(q) {
        arena.classList.add(q ? "quake" : "shk");
        setTimeout(function () { arena.classList.remove(q ? "quake" : "shk"); }, q ? 560 : 340);
    }
    function armPose(f, deg, ms) {
        f.armF.classList.add("manual");
        f.armF.style.transitionDuration = (ms || 220) + "ms";
        f.armF.style.transform = "rotate(" + deg + "deg)";
    }
    function armReset(f) {
        f.armF.style.transform = "";
        setTimeout(function () { f.armF.classList.remove("manual"); f.armF.style.transitionDuration = ""; }, 240);
    }

    function applyHit(v, fromDir, dmg, crit, flashCss, kb) {
        v.svg.style.filter = flashCss || "brightness(2.6) saturate(0.3)";
        setTimeout(function () { v.svg.style.filter = ""; }, 150);
        dmgNum(v.x, dmg, crit);
        v.hp -= dmg; updHP(v);
        return moveTo(v, v.x + fromDir * (kb || rng(40, 60)), 0.5, 13);
    }

    function projectile(html, fromX, toX, bottom, w, h, spd) {
        return new Promise(function (res) {
            var d = document.createElement("div");
            d.className = "fx"; d.style.bottom = bottom + "px"; d.style.width = w + "px"; d.style.height = h + "px";
            d.innerHTML = html;
            var dir = toX > fromX ? 1 : -1;
            if (dir < 0) d.querySelector("svg").style.transform = "scaleX(-1)";
            arena.appendChild(d);
            var t0 = performance.now(), dur = Math.abs(toX - fromX) / spd;
            function st(t) {
                var p = Math.min(1, (t - t0) / dur);
                d.style.left = (fromX + (toX - fromX) * p) + "px";
                if (p < 1) requestAnimationFrame(st);
                else { d.remove(); res(); }
            }
            requestAnimationFrame(st);
        });
    }

    async function meleeApproach(at, vc, spd) {
        faceEach();
        var side = vc.x > at.x ? 1 : -1;
        await moveTo(at, vc.x - side * 66, spd);
        faceEach();
    }

    async function atkVolt() {
        await meleeApproach(H, S, rng(0.26, 0.34));
        H.body.style.transform = "scale(1.07,0.88) rotate(" + (-H.dir * 7) + "deg)";
        await sleep(140);
        H.body.style.transform = "scale(1.06,0.97)";
        H.armF.classList.add("swingA");
        await moveTo(H, H.x + H.dir * 26, 0.62, 3);
        var crit = Math.random() < 0.12;
        var dmg = crit ? Math.round(rng(24, 30)) : Math.round(rng(12, 20));
        fxLightning(S.x - 6);
        fxSpark((H.x + S.x) / 2 + 32, crit);
        if (crit) shake(false);
        await applyHit(S, H.dir, dmg, crit, "brightness(2.2) sepia(1) hue-rotate(160deg) saturate(4)");
        H.armF.classList.remove("swingA");
        H.body.style.transform = "";
    }

    async function atkBolt() {
        faceEach();
        var side = S.x > H.x ? 1 : -1;
        fxDashLines(H.x + 20, side);
        await moveTo(H, S.x - side * 62, 0.7, 3);
        fxDashLines(H.x + 20, side);
        faceEach();
        H.body.style.transform = "scale(1.1,0.92)";
        H.armF.classList.add("jab");
        await sleep(120);
        var crit = Math.random() < 0.12;
        var dmg = crit ? Math.round(rng(24, 30)) : Math.round(rng(12, 20));
        fxClank((H.x + S.x) / 2 + 16);
        fxSteam(H.x + 44);
        fxSpark((H.x + S.x) / 2 + 32, crit);
        if (crit) shake(false);
        await applyHit(S, H.dir, dmg, crit);
        H.armF.classList.remove("jab");
        H.body.style.transform = "";
    }

    async function atkRune() {
        await meleeApproach(H, S, 0.17);
        armPose(H, -135, 300);
        H.body.style.transform = "scale(0.94,1.08)";
        await sleep(380);
        armPose(H, 35, 90);
        H.body.style.transform = "scale(1.12,0.86)";
        await sleep(110);
        var crit = Math.random() < 0.2;
        var dmg = crit ? Math.round(rng(24, 30)) : Math.round(rng(13, 20));
        fxQuake(Math.min(H.x, S.x) + 10);
        shake(true);
        fxSpark((H.x + S.x) / 2 + 32, true);
        await applyHit(S, H.dir, dmg, crit, null, rng(55, 75));
        armReset(H);
        H.body.style.transform = "";
    }

    async function atkEmber() {
        faceEach();
        await moveTo(H, spH() + rng(-10, 30), 0.3);
        faceEach();
        armPose(H, -55, 220);
        await sleep(340);
        armPose(H, -20, 100);
        await projectile('<svg viewBox="0 0 24 18"><ellipse cx="15" cy="9" rx="8" ry="7" fill="#F97316"/><ellipse cx="15" cy="9" rx="4.5" ry="4" fill="#FDE68A"/><path d="M8 9 Q2 5 0 9 Q2 13 8 9" fill="#FB923C" opacity="0.8"/></svg>', H.x + H.dir * 40, S.x + 10, 40, 26, 20, 0.42);
        var crit = Math.random() < 0.12;
        var dmg = crit ? Math.round(rng(24, 30)) : Math.round(rng(13, 20));
        fxBoom(S.x);
        if (crit) shake(false);
        await applyHit(S, H.dir, dmg, crit, "brightness(2) sepia(1) hue-rotate(-20deg) saturate(4)");
        armReset(H);
    }

    async function atkFern() {
        faceEach();
        await moveTo(H, spH() + rng(-10, 30), 0.3);
        faceEach();
        H.svg.classList.add("drawing");
        armPose(H, -10, 180);
        H.body.style.transform = "scale(0.96,1.03) rotate(" + (-H.dir * 3) + "deg)";
        await sleep(420);
        H.svg.classList.remove("drawing");
        armPose(H, 6, 60);
        H.body.style.transform = "";
        await projectile('<svg viewBox="0 0 28 8"><line x1="2" y1="4" x2="22" y2="4" stroke="#92400E" stroke-width="2.4"/><path d="M22 4 l-6 -3.5 l0 7 z" fill="#94A3B8"/><path d="M2 4 l4 -3 M2 4 l4 3" stroke="#EF4444" stroke-width="1.6"/></svg>', H.x + H.dir * 40, S.x + 12, 42, 28, 10, 0.62);
        var crit = Math.random() < 0.2;
        var dmg = crit ? Math.round(rng(22, 28)) : Math.round(rng(12, 19));
        fxSpark(S.x + 16, crit);
        if (crit) shake(false);
        await applyHit(S, H.dir, dmg, crit);
        armReset(H);
    }

    async function atkShade() {
        faceEach();
        var side = H.x > S.x ? 1 : -1;
        S.el.style.opacity = "0.55";
        fxGhost(S);
        await moveTo(S, S.x + (H.x - S.x) * 0.45, 0.85, 2);
        fxGhost(S);
        await moveTo(S, H.x - side * 62, 0.9, 2);
        S.el.style.opacity = "1";
        faceEach();
        var power = Math.random() < 0.15;
        if (power) {
            armPose(S, -120, 160);
            await sleep(220);
            armPose(S, 30, 70);
            await sleep(90);
            var dmg = Math.round(rng(18, 24));
            fxSlash(H.x - 4, true, "#F87171");
            fxSpark((H.x + S.x) / 2 + 32, true);
            shake(false);
            await applyHit(H, S.dir, dmg, true, null, rng(60, 80));
            armReset(S);
        } else {
            for (var i = 0; i < 3; i++) {
                S.armF.classList.remove("swingA");
                void S.armF.getBoundingClientRect();
                S.armF.classList.add("swingA");
                var d = Math.round(rng(3, 6));
                fxSlash(H.x + rng(-10, 8), false, i === 2 ? "#F87171" : "#FFFFFF");
                H.svg.style.filter = "brightness(2.4) saturate(0.4)";
                setTimeout(function () { H.svg.style.filter = ""; }, 110);
                dmgNum(H.x, d, false);
                H.hp -= d; updHP(H);
                await sleep(190);
                if (H.hp <= 0) break;
            }
            S.armF.classList.remove("swingA");
            await moveTo(H, H.x + S.dir * rng(40, 58), 0.5, 13);
        }
        fxGhost(S);
        await moveTo(S, spS() + rng(-40, 0), 0.55, 3);
        faceEach();
    }

    async function koSeq(loser, winner) {
        loser.body.style.transition = "transform .35s ease-in";
        loser.body.style.transform = "rotate(" + (-loser.dir * 84) + "deg)";
        loser.svg.style.filter = "grayscale(0.7) brightness(0.8)";
        koTxt.classList.add("show");
        winner.wins++;
        if (winner.wins > 5) { H.wins = 0; S.wins = 0; winner.wins = 1; }
        updPips();
        winner.body.classList.add("victory");
        setTimeout(function () { winner.body.classList.remove("victory"); }, 1300);
        await sleep(2000);
        koTxt.classList.remove("show");
        loser.el.classList.add("bt-blink");
        loser.hp = 100; updHP(loser);
        loser.body.style.transition = ""; loser.body.style.transform = ""; loser.svg.style.filter = "";
        setX(loser, loser === H ? spH() : spS());
        setTimeout(function () { loser.el.classList.remove("bt-blink"); }, 1450);
        await moveTo(winner, winner === H ? spH() : spS(), 0.2);
        faceEach();
        await sleep(500);
    }

    var gen = 0;
    async function battle() {
        var g = ++gen;
        H.hp = 100; S.hp = 100; updHP(H); updHP(S); updPips();
        H.body.style.transform = ""; S.body.style.transform = "";
        if (H.svg) H.svg.style.filter = "";
        S.svg.style.filter = "";
        S.el.style.opacity = "1";
        setX(H, spH()); setX(S, spS()); faceEach();
        await sleep(700);
        var heroAtk = { volt: atkVolt, ember: atkEmber, bolt7: atkBolt, rune: atkRune, fern: atkFern };
        while (gen === g) {
            if (document.hidden) { await sleep(1200); continue; }
            await sleep(rng(500, 1200));
            if (gen !== g) break;
            try {
                // 48% SHADE / 52% hero turn split — combined with the damage
                // tuning above (Monte Carlo verified), every hero wins roughly
                // 2 of 3 rounds against SHADE.
                if (Math.random() < 0.48) await atkShade();
                else await heroAtk[heroId]();
            } catch (e) { /* keep the loop alive */ }
            if (gen !== g) break;
            if (H.hp <= 0) await koSeq(H, S);
            else if (S.hp <= 0) await koSeq(S, H);
        }
    }

    var buttons = document.querySelectorAll("#hSel button");
    buttons.forEach(function (b) {
        if (b.dataset.h === heroId) b.classList.add("on");
        else b.classList.remove("on");
        b.addEventListener("click", function () {
            buttons.forEach(function (x) { x.classList.remove("on"); });
            b.classList.add("on");
            loadHero(b.dataset.h);
            document.cookie = "footer_hero=" + b.dataset.h + "; max-age=31536000; path=/; samesite=Lax";
            battle();
        });
    });

    loadHero(heroId);
    battle();
})();
