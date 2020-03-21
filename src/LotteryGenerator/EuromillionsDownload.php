<?php
/**
 * Helper class to download Lotto draw history file.
 */

declare(strict_types=1);

namespace MarkHeydon\LotteryGenerator;

/**
 * Helper class to download Lotto draw history file.
 *
 * @package MarkHeydon\LotteryGenerator
 * @since 1.0.0
 */
class EuromillionsDownload
{
    /** @var string URL of the draw history. */
    const HISTORY_DOWNLOAD_URL = 'https://www.national-lottery.co.uk/results/euromillions/draw-history/csv';
    /** @var string Filename to use for the local (data directory) file. */
    const FILENAME = 'euromillions-draw-history';

    /**
     * Download the Lotto draw history file.
     *
     * @since 1.0.0
     * @param bool $failDownload Simulate failed download (for testing).
     * @param bool $failRename Simulate failed rename of temp file (for testing).
     * @return string Error string on failure, otherwise empty string.
     */
    public static function download($failDownload = false, $failRename = false): string
    {
        $downloader = new Downloader(self::HISTORY_DOWNLOAD_URL, self::FILENAME);
        return $downloader->download($failDownload, $failRename);
    }

    /**
     * Uses the Euro Millions draw history to return a draws array.
     *
     * @since 1.0.0
     *
     * @return array The draws array.
     */
    public static function readEuromillionsDrawHistory(): array
    {
        $results = Utils::csvToArray(self::filePath());

        $allDraws = [];
        foreach ($results as $draw) {
            $drawDate = $draw['DrawDate'];
            $ball1 = $draw['Ball 1'];
            $ball2 = $draw['Ball 2'];
            $ball3 = $draw['Ball 3'];
            $ball4 = $draw['Ball 4'];
            $ball5 = $draw['Ball 5'];
            $luckyStar1 = $draw['Lucky Star 1'];
            $luckyStar2 = $draw['Lucky Star 2'];
            $raffles = isset($draw['UK Millionaire Maker']) ? explode(',', $draw['UK Millionaire Maker']) : [];
            $drawNumber = $draw['DrawNumber'];
            $dayOfDraw = date('l', strtotime($drawDate));

            $allDraws[] = [
                'drawNumber' => $drawNumber,
                'drawDate' => $drawDate,
                'drawDay' => $dayOfDraw,
                'ball1' => $ball1,
                'ball2' => $ball2,
                'ball3' => $ball3,
                'ball4' => $ball4,
                'ball5' => $ball5,
                'luckyStar1' => $luckyStar1,
                'luckyStar2' => $luckyStar2,
                'raffles' => $raffles,
            ];
        }

        return $allDraws;
    }

    /**
     * Full path to the downloaded results file.
     *
     * @return string String containing the full path of the results file.
     */
    private static function filePath(): string
    {
        $downloader = new Downloader(self::HISTORY_DOWNLOAD_URL, self::FILENAME);
        return $downloader->filePath();
    }
}
