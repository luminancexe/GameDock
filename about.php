<?php
include "includes/header.php";

$highlights = [
	[
		'title' => 'Reliable server-rendered foundation',
		'text' => 'Core workflows including discovery, checkout, rentals, selling, and community actions are handled with PHP for predictable behavior.',
	],
	[
		'title' => 'Consistent user interface',
		'text' => 'A shared styling system keeps pages, forms, and account screens visually consistent and easy to navigate.',
	],
	[
		'title' => 'Focused interactivity',
		'text' => 'JavaScript is applied where it adds clear value, improving usability and responsiveness without unnecessary complexity.',
	],
	[
		'title' => 'Platform-wide game experience',
		'text' => 'GameDock brings together marketplace listings, rentals, forum discussions, and account tools in a single platform.',
	],
];

$workflows = [
	'Browse PC, PlayStation, and Xbox game categories from one storefront.',
	'Buy, sell, and rent through streamlined server-rendered workflows.',
	'Track favorites, manage profile details, and participate in community discussions.',
];

$techStack = [
	'PHP for business logic and backend operations',
	'CSS for visual system, layout, and responsive presentation',
	'JavaScript for targeted interaction improvements',
	'Reusable includes for shared layout and utility behaviors',
];

$authors = [
	[
		'name' => '@luminancexe',
		'url' => 'https://github.com/luminancexe',
	],
	[
		'name' => '@abrar-0992',
		'url' => 'https://github.com/abrar-0992',
	],
	[
		'name' => '@Saadmantheretroenjoyer',
		'url' => 'https://github.com/Saadmantheretroenjoyer',
	],
	[
		'name' => '@ansm-muaaz',
		'url' => 'https://github.com/ansm-muaaz',
	],
];
?>

<main class="about-page">
	<section class="about-hero">
		<div class="container about-hero-inner">
			<div>
				<p class="about-kicker">About GameDock</p>
				<h1 class="about-title">A platform for game buying, selling, and rentals.</h1>
				<p class="about-lead">
					GameDock is a server-rendered web application designed for practical, reliable game marketplace workflows.
					The platform combines PHP backend logic, reusable frontend styling, and focused JavaScript enhancements
					to deliver a clear and maintainable user experience across browsing, transactions, and community features.
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
					<p>The project emphasizes clarity, maintainability, and dependable user-facing workflows.</p>
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
					<p>GameDock uses a modular layout so the codebase remains practical to maintain and extend.</p>
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
						The platform focuses on end-to-end game workflows, including catalog browsing, account management,
						wishlist and profile features, community forum participation, and purchase or rental flows.
						It follows a traditional PHP architecture for straightforward deployment and stable behavior.
					</p>
				</article>
			</div>
		</div>
	</section>

	<section class="about-section">
		<div class="container">
			<div class="section-head">
				<div>
					<h2>Authors</h2>
					<p>GameDock is built and maintained by the following contributors.</p>
				</div>
			</div>

			<div class="about-grid">
				<?php foreach ($authors as $author): ?>
					<article class="about-card">
						<h3><?php echo htmlspecialchars($author['name']); ?></h3>
						<p>
							GitHub:
							<a href="<?php echo htmlspecialchars($author['url']); ?>" target="_blank" rel="noopener noreferrer">
								<?php echo htmlspecialchars($author['url']); ?>
							</a>
						</p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
</main>

<?php include "includes/footer.php"; ?>
