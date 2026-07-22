<?php
include "includes/header.php";

$highlights = [
	[
		'title' => 'Backend-first',
		'text' => 'Core flows such as browsing, buying, renting, selling, and community interactions are handled on the server with PHP.',
	],
	[
		'title' => 'Unified UI',
		'text' => 'A shared CSS system keeps the experience consistent across platform pages, forms, and account screens.',
	],
	[
		'title' => 'Light interactivity',
		'text' => 'JavaScript is used sparingly for practical enhancements that improve navigation and usability without adding clutter.',
	],
	[
		'title' => 'Game community focus',
		'text' => 'The site brings together digital game listings, rentals, forum content, and user management in one place.',
	],
];

$workflows = [
	'Browse PC, PlayStation, and Xbox game sections from a single storefront.',
	'Buy, sell, or rent games through server-rendered flows that keep the experience simple.',
	'Use wishlist, profile, and forum features to stay connected with the platform.',
];

$techStack = [
	'PHP for page logic and backend operations',
	'CSS for layout, theming, and responsive presentation',
	'JavaScript for focused interaction enhancements',
	'Reusable includes for shared header, footer, and utility behavior',
];
?>

<main class="about-page">
	<section class="about-hero">
		<div class="container about-hero-inner">
			<div>
				<p class="about-kicker">About GameDock</p>
				<h1 class="about-title">A PHP platform for game buying, selling, and rentals.</h1>
				<p class="about-lead">
					GameDock is a server-rendered web application built around digital game workflows and community content.
					It combines PHP page logic, reusable CSS styling, and small JavaScript enhancements to deliver a clean,
					practical experience for browsing games, managing user activity, and exploring platform features.
				</p>
				<div class="about-actions">
					<a class="cta cta-own" href="pc_games.php">Browse PC Games</a>
					<a class="cta cta-rent" href="forum.php">Visit Community</a>
				</div>
			</div>

			<aside class="about-panel">
				<h2>What GameDock focuses on</h2>
				<ul class="about-list">
					<?php foreach ($workflows as $workflow): ?>
						<li><?php echo htmlspecialchars($workflow); ?></li>
					<?php endforeach; ?>
				</ul>
			</aside>
		</div>
	</section>

	<section class="about-section">
		<div class="container">
			<div class="section-head">
				<div>
					<h2>Core characteristics</h2>
					<p>The repository is intentionally simple on the frontend and practical on the backend.</p>
				</div>
			</div>

			<div class="about-grid">
				<?php foreach ($highlights as $highlight): ?>
					<article class="about-card">
						<h3><?php echo htmlspecialchars($highlight['title']); ?></h3>
						<p><?php echo htmlspecialchars($highlight['text']); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="about-section">
		<div class="container">
			<div class="section-head">
				<div>
					<h2>How it is built</h2>
					<p>GameDock keeps its structure modular so the pages stay maintainable and easy to extend.</p>
				</div>
			</div>

			<div class="about-stack">
				<article class="about-card">
					<h3>Technology stack</h3>
					<ul>
						<?php foreach ($techStack as $item): ?>
							<li><?php echo htmlspecialchars($item); ?></li>
						<?php endforeach; ?>
					</ul>
				</article>

				<article class="about-card">
					<h3>Project scope</h3>
					<p>
						The project is centered on game-related platform features, including browsing content, user accounts,
						wishlist and profile flows, a forum area, and checkout or rental processes. It is designed as a
						traditional PHP web application rather than a heavy single-page app.
					</p>
				</article>
			</div>
		</div>
	</section>
</main>

<?php include "includes/footer.php"; ?>
