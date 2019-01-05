<?php
/**
 * Unit tests for LottoGenerate class.
 */

namespace MarkHeydonTests\LotteryGenerator;

use MarkHeydon\LotteryGenerator\LottoGenerate;

/**
 * Unit tests for LottoGenerate class.
 *
 * @package MarkHeydonTests\LotteryGenerator
 */
class LottoGenerateTest extends GenerateTestCase
{
    /**
     * @inheritdoc
     */
    protected function generate(): array
    {
        return LottoGenerate::generate();
    }
}
