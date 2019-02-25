<?php
/**
 * Unit tests for ThunderballGenerate class.
 */

declare(strict_types=1);

namespace MarkHeydonTests\LotteryGenerator;

use MarkHeydon\LotteryGenerator\ThunderballGenerate;

/**
 * Unit tests for ThunderballGenerate class.
 *
 * @package MarkHeydonTests\LotteryGenerator
 */
class ThunderballGenerateTest extends GenerateTestCase
{
    /**
     * @inheritdoc
     */
    protected function generate(): array
    {
        return ThunderballGenerate::generate();
    }
}
