<?php
/**
 * Unit tests for LottoGenerate class.
 */

declare(strict_types=1);

namespace MarkHeydonTests\LotteryGenerator;

use MarkHeydon\LotteryGenerator\LottoHotpicksGenerate;

/**
 * Unit tests for LottoGenerate class.
 *
 * @package MarkHeydonTests\LotteryGenerator
 */
class LottoHotpicksGenerateTest extends GenerateTestCase
{
    /**
     * @inheritdoc
     */
    protected function generate(): array
    {
        return LottoHotpicksGenerate::generate();
    }
}
