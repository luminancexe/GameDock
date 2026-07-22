<?php
// Native Unicode emoji for forum reactions — rendered by the browser/OS's own
// emoji font, so no artwork to draw or license (unlike the custom SVG set this
// replaced).
$REACTION_ICONS = [
    'like' => ['emoji' => '👍', 'label' => 'Like'],
    'love' => ['emoji' => '❤️', 'label' => 'Love'],
    'haha' => ['emoji' => '😆', 'label' => 'Haha'],
    'wow' => ['emoji' => '😮', 'label' => 'Wow'],
    'sad' => ['emoji' => '😢', 'label' => 'Sad'],
    'angry' => ['emoji' => '😠', 'label' => 'Angry'],
];
// Reactions offered from the "like" trigger's hold-to-open picker.
// Dislike is intentionally excluded — it's its own standalone button.
$LIKE_FAMILY_TYPES = array_keys($REACTION_ICONS);

$REACTION_ICONS['dislike'] = ['emoji' => '👎', 'label' => 'Dislike'];
