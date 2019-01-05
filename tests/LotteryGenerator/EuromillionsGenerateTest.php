<?php
/**
 * Unit tests for LottoGenerate class.
 */

namespace MarkHeydonTests\LotteryGenerator;

use MarkHeydon\LotteryGenerator\EuromillionsGenerate;

/**
 * Unit tests for LottoGenerate class.
 *
 * @package MarkHeydonTests\LotteryGenerator
 */
class EuromillionsGenerateTest extends GenerateTestCase
{
    /**
     * @inheritdoc
     */
    protected function generate(): array
    {
        return EuromillionsGenerate::generate();
    }
}
