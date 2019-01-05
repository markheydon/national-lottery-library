<?php
/**
 * Unit tests for LottoDownload class.
 */

namespace MarkHeydonTests\LotteryGenerator;

use MarkHeydon\LotteryGenerator\LottoDownload;

/**
 * Unit tests for LottoDownload class.
 *
 * @package MarkHeydonTests\LotteryGenerator
 */
class LottoDownloadTest extends DownloaderTestCase
{
    /**
     * @inheritdoc
     */
    protected function download($failDownload = false, $failRename = false): string
    {
        return LottoDownload::download($failDownload, $failRename);
    }
}
