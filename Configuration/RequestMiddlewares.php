
<?php
return [
    'frontend' => [
        'madj2k/dr-sep/middleware/humanize-ai-content' => [
            'target' => \Madj2k\DrSerp\Middleware\HumanizeAiContent::class,
            'before' => [
                'typo3/cms-frontend/output-compression',
                'madj2k/accelerator/middleware/pseudo-cdn',
                'madj2k/accelerator/middleware/html-minify'
            ],
        ],
    ]
];
