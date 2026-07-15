<?php
require_once "includes/connection.php";
include "includes/header.php";

// Full catalog for the mouse-steered showcase, split into two rows that drift opposite ways
$stmt = $pdo->prepare("SELECT * FROM games WHERE status = 'Available' ORDER BY game_id ASC");
$stmt->execute();
$all_games = $stmt->fetchAll(PDO::FETCH_ASSOC);

$showcase_row1 = [];
$showcase_row2 = [];
foreach ($all_games as $i => $g) {
    if ($i % 2 === 0) {
        $showcase_row1[] = $g;
    } else {
        $showcase_row2[] = $g;
    }
}
?>

<section class="hero">
    <div class="hero-field" id="heroField">
        <div class="hero-glow own"></div>
        <div class="hero-glow rent"></div>

        <div class="hero-shape" data-depth="0.09" style="width:130px;height:81px; top:16%; left:7%; transform:rotate(-14deg);">
            <svg viewBox="0 0 160 100" fill="none"><path d="M50 20 L110 20 Q140 20 145 55 L150 85 Q152 100 138 100 Q128 100 122 90 L110 72 H50 L38 90 Q32 100 22 100 Q8 100 10 85 L15 55 Q20 20 50 20 Z" stroke="#E8A33D" stroke-width="2.5"/><rect x="44" y="40" width="8" height="26" rx="2" stroke="#E8A33D" stroke-width="2" opacity=".7"/><rect x="33" y="51" width="30" height="8" rx="2" stroke="#E8A33D" stroke-width="2" opacity=".7"/><circle cx="112" cy="38" r="4.5" stroke="#E8A33D" stroke-width="2" opacity=".7"/><circle cx="112" cy="60" r="4.5" stroke="#E8A33D" stroke-width="2" opacity=".7"/><circle cx="101" cy="49" r="4.5" stroke="#E8A33D" stroke-width="2" opacity=".7"/><circle cx="123" cy="49" r="4.5" stroke="#E8A33D" stroke-width="2" opacity=".7"/></svg>
        </div>
        <div class="hero-shape" data-depth="0.14" style="width:92px;height:57px; top:64%; left:14%; transform:rotate(9deg);">
            <svg viewBox="0 0 160 100" fill="none"><path d="M50 20 L110 20 Q140 20 145 55 L150 85 Q152 100 138 100 Q128 100 122 90 L110 72 H50 L38 90 Q32 100 22 100 Q8 100 10 85 L15 55 Q20 20 50 20 Z" stroke="#4FD1D9" stroke-width="2.5"/><rect x="44" y="40" width="8" height="26" rx="2" stroke="#4FD1D9" stroke-width="2" opacity=".7"/><rect x="33" y="51" width="30" height="8" rx="2" stroke="#4FD1D9" stroke-width="2" opacity=".7"/><circle cx="112" cy="38" r="4.5" stroke="#4FD1D9" stroke-width="2" opacity=".7"/><circle cx="112" cy="60" r="4.5" stroke="#4FD1D9" stroke-width="2" opacity=".7"/><circle cx="101" cy="49" r="4.5" stroke="#4FD1D9" stroke-width="2" opacity=".7"/><circle cx="123" cy="49" r="4.5" stroke="#4FD1D9" stroke-width="2" opacity=".7"/></svg>
        </div>
        <div class="hero-shape" data-depth="0.06" style="width:140px;height:87px; top:20%; right:9%; transform:rotate(11deg);">
            <svg viewBox="0 0 160 100" fill="none"><path d="M50 20 L110 20 Q140 20 145 55 L150 85 Q152 100 138 100 Q128 100 122 90 L110 72 H50 L38 90 Q32 100 22 100 Q8 100 10 85 L15 55 Q20 20 50 20 Z" stroke="#4FD1D9" stroke-width="2.5"/><rect x="44" y="40" width="8" height="26" rx="2" stroke="#4FD1D9" stroke-width="2" opacity=".7"/><rect x="33" y="51" width="30" height="8" rx="2" stroke="#4FD1D9" stroke-width="2" opacity=".7"/><circle cx="112" cy="38" r="4.5" stroke="#4FD1D9" stroke-width="2" opacity=".7"/><circle cx="112" cy="60" r="4.5" stroke="#4FD1D9" stroke-width="2" opacity=".7"/><circle cx="101" cy="49" r="4.5" stroke="#4FD1D9" stroke-width="2" opacity=".7"/><circle cx="123" cy="49" r="4.5" stroke="#4FD1D9" stroke-width="2" opacity=".7"/></svg>
        </div>
        <div class="hero-shape" data-depth="0.11" style="width:84px;height:52px; top:68%; right:14%; transform:rotate(-8deg);">
            <svg viewBox="0 0 160 100" fill="none"><path d="M50 20 L110 20 Q140 20 145 55 L150 85 Q152 100 138 100 Q128 100 122 90 L110 72 H50 L38 90 Q32 100 22 100 Q8 100 10 85 L15 55 Q20 20 50 20 Z" stroke="#E8A33D" stroke-width="2.5"/><rect x="44" y="40" width="8" height="26" rx="2" stroke="#E8A33D" stroke-width="2" opacity=".7"/><rect x="33" y="51" width="30" height="8" rx="2" stroke="#E8A33D" stroke-width="2" opacity=".7"/><circle cx="112" cy="38" r="4.5" stroke="#E8A33D" stroke-width="2" opacity=".7"/><circle cx="112" cy="60" r="4.5" stroke="#E8A33D" stroke-width="2" opacity=".7"/><circle cx="101" cy="49" r="4.5" stroke="#E8A33D" stroke-width="2" opacity=".7"/><circle cx="123" cy="49" r="4.5" stroke="#E8A33D" stroke-width="2" opacity=".7"/></svg>
        </div>
    </div>

    <p class="hero-eyebrow">Digital <em>Ownership</em> &nbsp;&middot;&nbsp; Console <i>Rentals</i></p>

    <h1 class="hero-headline">
        <span class="row"><span>Own It.</span></span>
        <span class="row"><span>Outright.</span></span>
        <span class="row"><span>Or Rent It.</span></span>
        <span class="row"><span>On Loan.</span></span>
    </h1>

    <p class="hero-sub">GameDock is one storefront, two ways to play: <b>buy PC keys forever</b>, or <b>borrow PS discs by the week.</b></p>

    <div class="hero-ctas">
        <a href="pc_games.php" class="cta cta-own">Browse PC Games &rarr;</a>
        <a href="ps_rentals.php" class="cta cta-rent">Rent PS Games &rarr;</a>
    </div>

    <div class="hero-scrollcue"><div class="pill"></div><span>Scroll</span></div>
</section>

<div class="container mt-4">
    <div class="section-head">
        <div>
            <h2>Explore the Library</h2>
            <p>Every game on GameDock, always in motion &mdash; move your mouse left or right to steer it.</p>
        </div>
    </div>
</div>

<div class="showcase" id="showcase">
    <?php if (count($all_games) > 0): ?>
        <?php
        $rows = [ ['id' => 'track1', 'games' => $showcase_row1], ['id' => 'track2', 'games' => $showcase_row2] ];
        foreach ($rows as $row):
        ?>
        <div class="marquee-row">
            <div class="marquee-track" id="<?php echo $row['id']; ?>">
                <?php for ($rep = 0; $rep < 2; $rep++): ?>
                <div class="marquee-group"<?php echo $rep ? ' aria-hidden="true"' : ''; ?>>
                    <?php foreach ($row['games'] as $game): ?>
                        <?php $is_pc = $game['platform'] === 'PC'; ?>
                        <a class="thumb <?php echo $is_pc ? 'own' : 'rent'; ?>" href="game_details.php?id=<?php echo $game['game_id']; ?>" title="<?php echo htmlspecialchars($game['title']); ?>"<?php echo $rep ? ' tabindex="-1"' : ''; ?>>
                            <span class="tag <?php echo $is_pc ? 'own' : 'rent'; ?>"><?php echo $is_pc ? 'Own' : 'Rent'; ?></span>
                            <img src="uploads/<?php echo htmlspecialchars($game['image']); ?>" alt="<?php echo htmlspecialchars($game['title']); ?>" loading="lazy" onerror="this.src='https://via.placeholder.com/300x400?text=No+Image'">
                        </a>
                    <?php endforeach; ?>
                </div>
                <?php endfor; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="container">No games available yet &mdash; check back soon.</p>
    <?php endif; ?>
</div>

<?php include "includes/footer.php"; ?>
