<?php
/**
 * Helper class to download Lotto draw history file.
 *
 * @package MarkHeydon
 * @subpackage MarkHeydon\LotteryGenerator
 * @since 1.0.0
 */

namespace MarkHeydon\LotteryGenerator;


class EuromillionsDownload
{
    const HISTORY_DOWNLOAD_URL = 'https://www.national-lottery.co.uk/results/euromillions/draw-history/csv';
    const FILENAME = 'euromillions-draw-history';

    /**
     * Download the Lotto draw history file.
     *
     * @since 1.0.0
     *
     * @return string Error string on failure, otherwise empty string.
     */
    public static function download(): string
    {
        $downloader = new Downloader(self::HISTORY_DOWNLOAD_URL, self::FILENAME);
        return $downloader->download();
    }

    /**
     * Full path to the downloaded results file.
     *
     * @return string String containing the full path of the results file.
     */
    public static function filePath(): string
    {
        $downloader = new Downloader(self::HISTORY_DOWNLOAD_URL, self::FILENAME);
        return $downloader->filePath();
    }

}