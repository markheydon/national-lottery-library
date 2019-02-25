<?php
/**
 * Helper class to generate numbers for the Lotto game.
 */

declare(strict_types=1);

namespace MarkHeydon\LotteryGenerator;

/**
 * Helper class to generate numbers for the Lotto game.
 *
 * @package MarkHeydon\LotteryGenerator
 * @since 1.0.0
 */
class LottoHotpicksGenerate extends LottoGenerate
{
    /**
     * @inheritdoc
     */
    protected static function getNameOfGame(): string
    {
        return 'LottoHotpicks';
    }

    /**
     * @inheritdoc
     */
    protected static function getNumOfBalls(): int
    {
        return 5;
    }

    /**
     * Array of ball names.
     *
     * Balls 1 ~ 5.
     *
     * @return array Array of ball names.
     */
    protected static function getBallNames(): array
    {
        $ballNames = [];
        for ($b = 1; $b <= static::getNumOfBalls(); $b++) {
            $ballNumber = 'ball' . $b;
            $ballNames[] = $ballNumber;
        }
        return $ballNames;
    }
}
