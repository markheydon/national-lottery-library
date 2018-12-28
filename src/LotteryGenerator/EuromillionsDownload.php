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
    /**
     * Download the Lotto draw history file.
     *
     * @since 1.0.0
     *
     * @return string Error string on failure, otherwise empty string.
     */
    public static function download(): string
    {
        $url = 'https://www.national-lottery.co.uk/results/euromillions/draw-history/csv';
        $filename = 'euromillions-draw-history';
        $downloader = new Downloader($url, $filename);
        return $downloader->download();
    }
}