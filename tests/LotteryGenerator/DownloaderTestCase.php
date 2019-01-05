<?php
/** Base Test Case for Download lottery unit tests. */

declare(strict_types=1);

namespace MarkHeydonTests\LotteryGenerator;

use PHPUnit\Framework\TestCase;

abstract class DownloaderTestCase extends TestCase
{
    /**
     * Generated download output for use tests.
     *
     * @param bool $failDownload Simulate failed download (for testing).
     * @param bool $failRename Simulate failed rename of temp file (for testing).
     *
     * @return string Generated download output for use tests.
     */
    abstract protected function download($failDownload = false, $failRename = false): string;

    /**
     * Tests the download() methods works without error.
     *
     * Only thing that would stop this working is network issues,
     * in theory at least.
     */
    public function testDownloadOK()
    {
        $result = $this->download();
        $this->assertEmpty($result);
    }

    /**
     * Tests the download() method reports error on failed download.
     */
    public function testDownloadFailed()
    {
        $result = $this->download(true, false);
        $this->assertContains('failed', $result);
    }

    /**
     * Test the download() method reports error on failed renaming of temp file.
     */
    public function testDownloadRenameFailed()
    {
        $result = $this->download(false, true);
        $this->assertContains('failed', $result);
    }
}
