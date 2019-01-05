<?php
/**
 * Unit tests for EuromillionsDownload class.
 */

namespace MarkHeydonTests\LotteryGenerator;

use MarkHeydon\LotteryGenerator\EuromillionsDownload;

/**
 * Unit tests for EuromillionsDownload class.
 *
 * @package MarkHeydonTests\LotteryGenerator
 */
class EuromillionsDownloadTest extends DownloaderTestCase
{
    /**
     * @inheritdoc
     */
    protected function download($failDownload = false, $failRename = false): string
    {
        return EuromillionsDownload::download($failDownload, $failRename);
    }
}
