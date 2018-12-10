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
        'route' => 'generate-lotto [--others] [--verbose]',
        'description' => 'Generate random numbers for the Lotto game.',
        'short_description' => 'Generate random numbers for the Lotto game.',
        'options_descriptions' => [
            '--others' => 'Include other lines, not just suggested.',
            '--verbose' => 'Include all method information.',
        ],
    ],
];