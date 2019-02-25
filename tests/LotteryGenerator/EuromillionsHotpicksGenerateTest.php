<?php
/**
 * Unit tests for LottoGenerate class.
 */

declare(strict_types=1);

namespace MarkHeydonTests\LotteryGenerator;

use MarkHeydon\LotteryGenerator\EuromillionsHotpicksGenerate;

/**
 * Unit tests for LottoGenerate class.
 *
 * @package MarkHeydonTests\LotteryGenerator
 */
class EuromillionsHotpicksGenerateTest extends GenerateTestCase
{
    /**
     * @inheritdoc
     */
    protected function generate(): array
    {
        return EuromillionsHotpicksGenerate::generate();
    }
}
