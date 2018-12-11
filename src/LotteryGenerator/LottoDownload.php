<?php
/**
 * Helper class to download Lotto draw history file.
 *
 * @package MarkHeydon
 * @subpackage MarkHeydon\LotteryGenerator
 * @since 1.0.0
 */

namespace MarkHeydon\LotteryGenerator;


class LottoDownload
{
    const FILENAME = 'lotto-draw-history.csv';
    const DIR_PATH = __DIR__ . '/../../data';
    const FILE_PATH = self::DIR_PATH . '/' . self::FILENAME;

    const HISTORY_DOWNLOAD_URL = 'https://www.national-lottery.co.uk/results/lotto/draw-history/csv';

    public static function download(): string
    {
        // workout a filename to rename the current file to (if there is one)
        $timestamp = date('YmdHis', time());
        $renameFilepath = (file_exists(self::FILE_PATH))
            ? self::DIR_PATH . '/lotto-draw-history' . $timestamp . '.csv' : '';

        // download new file
        // if it worked, then rename existing and replace with new
        // otherwise report failure
        $tempFilename = tempnam(sys_get_temp_dir(), 'lotto-draw-history');
        $result = file_put_contents($tempFilename, fopen(self::HISTORY_DOWNLOAD_URL, 'r'));
        if (false === $result) {
            return 'Download failed';
        }
        if (strlen($renameFilepath) > 0) {
            $result = rename(self::FILE_PATH, $renameFilepath);
            if (false === $result) {
                return 'Renaming of old history file failed';
            }
        }
        $result = rename($tempFilename, self::FILE_PATH);
        if (false === $result) {
            return 'Renaming of newly download history file failed';
        }
        return '';
    }
}