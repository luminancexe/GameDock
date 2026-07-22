<?php
require_once "includes/connection.php";

$posts = [
    [
        'user_id' => 1,
        'title' => 'Anyone excited for the new GTA 6 trailer?',
        'content' => 'I just saw the latest rumors and it looks like we might get another trailer next month. What features are you all hoping to see in the new game?'
    ],
    [
        'user_id' => 1,
        'title' => 'Best build for Elden Ring DLC?',
        'content' => 'I am struggling with the new bosses in Shadow of the Erdtree. Does anyone have a good strength/faith build recommendation? Using the Blasphemous Blade right now.'
    ],
    [
        'user_id' => 1,
        'title' => 'GameDock PS5 Rental Review',
        'content' => 'Just rented Ghost of Tsushima from here. The process was super smooth and the game is absolutely gorgeous on the PS5. Highly recommend the rental service if you want to try before you buy.'
    ],
    [
        'user_id' => 1,
        'title' => 'Tips for selling used Xbox games?',
        'content' => 'Looking to sell my collection of Xbox One games. Any advice on pricing and shipping? Has anyone sold through GameDock before?'
    ],
    [
        'user_id' => 1,
        'title' => 'Starfield - worth the hype?',
        'content' => 'Thinking about renting Starfield to see if it lives up to all the buzz. 100+ hours of gameplay right? Let me know if you think it is worth the time investment.'
    ],
    [
        'user_id' => 1,
        'title' => 'Best indie games of 2024',
        'content' => 'What are your favorite indie games released this year? I am tired of AAA titles and want to discover some hidden gems. Any recommendations?'
    ],
    [
        'user_id' => 1,
        'title' => 'Cyberpunk 2077 still worth playing?',
        'content' => 'Just heard about the new patches. Is the game in a good place now? Thinking about renting it to check out the improvements.'
    ]
];

try {
    $stmt = $pdo->prepare("INSERT INTO forum_posts (user_id, title, content) VALUES (?, ?, ?)");
    foreach ($posts as $post) {
        $stmt->execute([$post['user_id'], $post['title'], $post['content']]);
    }
    echo "Sample posts added successfully.";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
