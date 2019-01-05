<?php
/**
 * Helper class to download draw history files.
 */

namespace MarkHeydon\LotteryGenerator;

/**
 * Helper class to download draw history files.
 *
 * @package MarkHeydon\LotteryGenerator
 * @since 1.0.0
 */
class Downloader
{
    /** @var string The data folder path. */
    private const DATA_PATH = __DIR__ . '/../../data';

    /** @var string Filename to use for successful download (excluding .csv). */
    private $filename;
    /** @var string URL to download from. */
    private $url;

    /**
     * Returns the full filepath of the download file.
     *
     * Including the .csv suffix.
     *
     * @since 1.0.0
     *
     * @return string Full path of the download file.
     */
    public function filePath(): string
    {
        return self::DATA_PATH . '/' . $this->filename . '.csv';
    }

    /**
     * Downloader constructor.
     *
     * @since 1.0.0
     *
     * @param string $url URL to use to download from.
     * @param string $filename Filename for local file excluding .csv extension.
     */
    public function __construct(string $url, string $filename)
    {
        $this->url = $url;
        $this->filename = $filename;
    }

    /**
     * Download the draw history file to the 'data' directory.
     *
     * @since 1.0.0
     *
     * @return string Error string on failure, otherwise empty string.
     */
    public function download(): string
    {
        // workout a filename to rename the current file to (if there is one)
        $timestamp = date('YmdHis', time());
        $renameFilepath = (file_exists($this->filePath()))
            ? self::DATA_PATH . '/' . $this->filename . '-' . $timestamp . '.csv' : '';

        // download new file
        // if it worked, then rename existing and replace with new
        // otherwise report failure
        $tempFilename = tempnam(sys_get_temp_dir(), 'lotto-draw-history');
        $result = file_put_contents($tempFilename, fopen($this->url, 'r'));
        if (false === $result) {
            return 'Download failed';
        }
        if (strlen($renameFilepath) > 0) {
            $result = rename($this->filePath(), $renameFilepath);
            if (false === $result) {
                return 'Renaming of old history file failed';
            }
        }
        $result = rename($tempFilename, $this->filePath());
        if (false === $result) {
            return 'Renaming of newly download history file failed';
        }
        return '';
    }
}
