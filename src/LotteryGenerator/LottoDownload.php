<?php
/**
 * Helper class to download Lotto draw history file.
 */

namespace MarkHeydon\LotteryGenerator;

/**
 * Helper class to download Lotto draw history file.
 *
 * @package MarkHeydon\LotteryGenerator
 * @since 1.0.0
 */
class LottoDownload
{
    /** @var string URL of the draw history. */
    const HISTORY_DOWNLOAD_URL = 'https://www.national-lottery.co.uk/results/lotto/draw-history/csv';
    /** @var string Filename to use for the local (data directory) file. */
    const FILENAME = 'lotto-draw-history';

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
     * Uses the Lotto draw history to return a draws array.
     *
     * @since 1.0.0
     *
     * @return array The draws array.
     */
    public static function readLottoDrawHistory(): array
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
            $ball6 = $draw['Ball 6'];
            $bonusBall = $draw['Bonus Ball'];
            $ballSet = $draw['Ball Set'];
            $machine = $draw['Machine'];
            $raffles = self::parseRafflesString($draw['Raffles']);
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
                'ball6' => $ball6,
                'bonusBall' => $bonusBall,
                'ballSet' => $ballSet,
                'machine' => $machine,
                'raffles' => $raffles,
            ];
        }

        return $allDraws;
    }

    /**
     * Returns array of raffle numbers from the specified string.
     *
     * CSV history file seems to be in the format 'RAF;RAF,RAF,RAF'.
     *
     * @since 1.0.0
     *
     * @param string $raffles String from CSV history file for field 'Raffles'.
     * @return array Array of strings of all the raffles numbers from the specified string.
     */
    private static function parseRafflesString(string $raffles): array
    {
        if (empty($raffles)) {
            return [];
        }
        $split = explode(';', $raffles);
        $first = $split[0];
        $others = explode(',', $split[1]);
        $result = array_merge([$first], $others);
        return $result;
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