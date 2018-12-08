<?php
/**
 * Returns the routes array.
 *
 * @since 1.0.0
 */
return [
    [
        'name' => 'generate-lotto',
        'handler' => [
            new MarkHeydon\LotteryGeneratorCLI(),
            'generateLotto'
        ],
        'description' => 'Generate random numbers for the Lotto game.',
        'short_description' => 'Generate random numbers for the Lotto game.',
    ],
];