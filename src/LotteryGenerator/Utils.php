<?php
/**
 * Various helper/utility methods.
 *
 * @package MarkHeydon
 * @subpackage MarkHeydon\LotteryGenerator
 * @since 1.0.0
 */

namespace MarkHeydon\LotteryGenerator;

class Utils
{
    /**
     * Helper method to convert a csv file to an associative array.
     *
     * @since 1.0.0
     *
     * @param string $filename Full filename to the csv file to process.
     * @param string $delimiter Optional delimiter to use if not standard ','.
     * @return array|bool Associative array or false if there was an issue parsing.
     */
    public static function csvToArray($filename = '', $delimiter = ','): array
    {
        $header = null;
        $data = [];
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header) {
                    $header = $row;
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }
        return $data;
    }

}