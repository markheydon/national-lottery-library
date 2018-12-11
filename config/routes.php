<?php
/**
 * Returns the routes array.
 *
 * @since 1.0.0
 */
return [
    [
        'name' => 'lotto-generate',
        'handler' => 'MarkHeydon\LotteryGeneratorCLI::generateLotto',
        'route' => 'lotto-generate [--others] [--verbose]',
        'description' => 'Generate random numbers for the Lotto game.',
        'short_description' => 'Generate random numbers for the Lotto game.',
        'options_descriptions' => [
            '--others' => 'Include other lines, not just suggested.',
            '--verbose' => 'Include all method information.',
        ],
    ],
    [
        'name' => 'lotto-download',
        'handler' => 'MarkHeydon\LotteryGeneratorCLI::downloadLotto',
        'route' => 'lotto-download',
        'description' => 'Download latest Lotto history draw file.',
    ]
];