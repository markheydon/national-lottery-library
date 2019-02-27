<?php
/**
 * Unit tests for ThunderballDownload class.
 */

declare(strict_types=1);

namespace MarkHeydonTests\LotteryGenerator;

use MarkHeydon\LotteryGenerator\ThunderballDownload;

/**
 * Unit tests for LottoDownload class.
 *
 * @package MarkHeydonTests\LotteryGenerator
 */
class ThunderballDownloadTest extends DownloaderTestCase
{
    /**
     * @inheritdoc
     */
    protected function download($failDownload = false, $failRename = false): string
    {
        return ThunderballDownload::download($failDownload, $failRename);
    }
}
