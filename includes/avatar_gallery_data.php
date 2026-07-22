<?php
// Static gender bucketing for the bundled avatar gallery (uploads/avatars/gallery/).
// The source pack ships with no gender metadata, so this is a best-effort visual
// sort based on hairstyle/accessory styling — adjust freely by moving filenames
// between the two arrays below.
$AVATAR_GALLERY = [
    'male' => [
        'avatar_001.png', 'avatar_002.png', 'avatar_005.png', 'avatar_008.png', 'avatar_011.png',
        'avatar_012.png', 'avatar_013.png', 'avatar_014.png', 'avatar_015.png', 'avatar_017.png',
        'avatar_018.png', 'avatar_021.png', 'avatar_022.png', 'avatar_023.png', 'avatar_024.png',
        'avatar_025.png', 'avatar_027.png', 'avatar_028.png', 'avatar_029.png', 'avatar_031.png',
        'avatar_032.png', 'avatar_033.png', 'avatar_036.png', 'avatar_039.png', 'avatar_040.png',
        'avatar_042.png', 'avatar_043.png', 'avatar_045.png', 'avatar_046.png', 'avatar_047.png',
        'avatar_048.png', 'avatar_049.png', 'avatar_050.png', 'avatar_052.png', 'avatar_053.png',
        'avatar_058.png', 'avatar_060.png', 'avatar_062.png', 'avatar_063.png', 'avatar_065.png',
        'avatar_066.png', 'avatar_068.png', 'avatar_069.png', 'avatar_070.png', 'avatar_072.png',
        'avatar_074.png', 'avatar_077.png', 'avatar_078.png', 'avatar_079.png', 'avatar_080.png',
    ],
    'female' => [
        'avatar_003.png', 'avatar_004.png', 'avatar_006.png', 'avatar_007.png', 'avatar_009.png',
        'avatar_010.png', 'avatar_016.png', 'avatar_019.png', 'avatar_020.png', 'avatar_026.png',
        'avatar_030.png', 'avatar_034.png', 'avatar_035.png', 'avatar_037.png', 'avatar_038.png',
        'avatar_041.png', 'avatar_044.png', 'avatar_051.png', 'avatar_054.png', 'avatar_055.png',
        'avatar_056.png', 'avatar_057.png', 'avatar_059.png', 'avatar_061.png', 'avatar_064.png',
        'avatar_067.png', 'avatar_071.png', 'avatar_073.png', 'avatar_075.png', 'avatar_076.png',
        'avatar_081.png',
    ],
];

function avatar_gallery_all_files() {
    global $AVATAR_GALLERY;
    return array_merge($AVATAR_GALLERY['male'], $AVATAR_GALLERY['female']);
}

// Gives anything without a chosen avatar a distinct gallery character instead of
// the generic controller icon everyone shares. Deterministic (same seed always
// gives the same face, so it's stable across reloads) but scattered via a simple
// multiplicative hash rather than plain modulo, so consecutive seeds (e.g. post
// IDs 1, 2, 3...) don't land on adjacent, visibly-sequential avatars.
function default_avatar_for_seed($seed) {
    $all = avatar_gallery_all_files();
    if (empty($all)) {
        return 'default.png';
    }
    $index = ($seed * 2654435761) % count($all);
    return 'gallery/' . $all[$index];
}
