<?php
/**
 * Lottery Generator CLI Handler.
 */

namespace MarkHeydon;

use MarkHeydon\LotteryGenerator\EuromillionsDownload;
use MarkHeydon\LotteryGenerator\EuromillionsGenerate;
use MarkHeydon\LotteryGenerator\LottoDownload;
use MarkHeydon\LotteryGenerator\LottoGenerate;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Console\ColorInterface;
use ZF\Console\Route;

/**
 * Lottery Generator CLI Handler.
 *
 * Dispatches commands off to relevant class.
 *
 * @package MarkHeydon
 * @since 1.0.0
 */
class LotteryGeneratorCLI
{
    /**
     * Download Lotto draw history file and report success (or failure).
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     *
     * @param Route $route The ZF\Console\Route instance from the Dispatcher.
     * @param Console $console The Zend\Console adapter currently in use.
     */
    public static function downloadLotto(Route $route, Console $console): void
    {
        $success = LottoDownload::download();
        if (strlen($success) < 1) {
            $console->writeLine('Success.', ColorInterface::GREEN);
        } else {
            $console->writeLine('Failed: ' . $success . '.', ColorInterface::RED);
        }
    }

    /**
     * Download Lotto draw history file and report success (or failure).
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     *
     * @param Route $route The ZF\Console\Route instance from the Dispatcher.
     * @param Console $console The Zend\Console adapter currently in use.
     */
    public static function downloadEuromillions(Route $route, Console $console): void
    {
        $success = EuromillionsDownload::download();
        if (strlen($success) < 1) {
            $console->writeLine('Success.', ColorInterface::GREEN);
        } else {
            $console->writeLine('Failed: ' . $success . '.', ColorInterface::RED);
        }
    }

    /**
     * Generate Lotto numbers and output.
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     *
     * @param Route $route The ZF\Console\Route instance from the Dispatcher.
     * @param Console $console The Zend\Console adapter currently in use.
     */
    public static function generateLotto(Route $route, Console $console): void
    {
        // Generator generates from a number of methods.  We want the first from each method as the
        // 'suggested' numbers to use, followed by the rest.
        $results = LottoGenerate::generate();
        self::outputSuggestedEtc($route, $console, $results);
    }

    /**
     * Generate EuroMillions numbers and output.
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     *
     * @param Route $route The ZF\Console\Route instance from the Dispatcher.
     * @param Console $console The Zend\Console adapter currently in use.
     */
    public static function generateEuromillions(Route $route, Console $console): void
    {
        // Generator generates from a number of methods.  We want the first from each method as the
        // 'suggested' numbers to use, followed by the rest.
        $results = EuromillionsGenerate::generate();
        self::outputSuggestedEtc($route, $console, $results);
    }

    /**
     * Output results formatted by suggested etc.
     *
     * @param Route $route The ZF\Console\Route instance from the Dispatcher.
     * @param Console $console The Zend\Console adapter currently in use.
     * @param array $results Results array to output.
     */
    private static function outputSuggestedEtc(Route $route, Console $console, array $results): void
    {
        // Command line flags
        $verboseMode = $route->getMatchedParam('verbose', false);
        $includeOthers = $route->getMatchedParam('others', false);

        $suggested = [];
        $others = [];
        foreach ($results as $method) {
            $suggested[] = array_shift($method);
            if ($includeOthers) {
                $others = array_merge($others, $method);
            }
        }

        $console->writeLine('Suggested', ColorInterface::GREEN);
        $console->writeLine('=========', ColorInterface::GREEN);
        $console->writeLine();
        LotteryGeneratorCLI::outputLines($console, $suggested);
        $console->writeLine();

        if (count($others) > 0) {
            $console->writeLine('Others', ColorInterface::LIGHT_GREEN);
            $console->writeLine('======', ColorInterface::LIGHT_GREEN);
            $console->writeLine();
            LotteryGeneratorCLI::outputLines($console, $others);
            $console->writeLine();
        }

        if ($verboseMode) {
            $console->writeLine();
            $console->writeLine('Verbose', ColorInterface::RED);
            $console->writeLine('=======', ColorInterface::RED);
            // Iterate through all methods and results in verbose mode
            foreach ($results as $method => $line) {
                $console->writeLine('Method: ' . $method);
                LotteryGeneratorCLI::outputLines($console, $line);
                $console->writeLine();
            }
        }
    }

    /**
     * Output the numbers from the array of passed in lines.
     *
     * @param Console $console Console object to output to.
     * @param array $lines Lines to output.
     */
    private static function outputLines(Console $console, array $lines): void
    {
        $ctr = 0;
        foreach ($lines as $line) {
            $ctr++;
            $console->write('Line ' . $ctr . ': ');
            if (isset($line['luckyStars'])) { // is EuroMillions
                $aLine = $line['normal'];
                while (($ball = array_shift($aLine)) !== null) {
                    $console->write(str_pad($ball, 2, '0', STR_PAD_LEFT));
                    if (count($aLine) > 0) {
                        $console->write(' - ');
                    }
                }
                $console->write(implode(' - ', $aLine));

                $console->write('    ');

                $aLine = $line['luckyStars'];
                while (($ball = array_shift($aLine)) !== null) {
                    $console->write(str_pad($ball, 2, '0', STR_PAD_LEFT));
                    if (count($aLine) > 0) {
                        $console->write(' - ');
                    }
                }
                $console->write(implode(' - ', $aLine));

                $console->writeLine();
            } else {
                while (($ball = array_shift($line)) !== null) {
                    $console->write(str_pad($ball, 2, '0', STR_PAD_LEFT));
                    if (count($line) > 0) {
                        $console->write(' - ');
                    }
                }
                $console->write(implode(' - ', $line));
                $console->writeLine();
            }
        }
    }
}