<?php
/**
 * Helper class to generate numbers for the EuroMillions game.
 */

declare(strict_types=1);

namespace MarkHeydon\LotteryGenerator;

/**
 * Helper class to generate numbers for the EuroMillions Hotpicks game.
 *
 * @package MarkHeydon\LotteryGenerator
 * @since 1.0.0
 */
class EuromillionsHotpicksGenerate extends EuromillionsGenerate
{
    /**
     * @inheritdoc
     */
    protected static function getNameOfGame(): string
    {
        return 'EuroMillions Hotpicks';
    }

}
