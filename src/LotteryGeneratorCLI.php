<?php
/**
 * Lottery Generator CLI Handler.
 *
 * Dispatches commands off to relevant class.
 *
 * @package MarkHeydon
 * @since 1.0.0
 */

namespace MarkHeydon;

use MarkHeydon\LotteryGenerator\GenerateLotto;
use Zend\Console\Adapter\AdapterInterface as Console;
use ZF\Console\Route;

/**
 * Class LotteryGeneratorCLI
 *
 * @since 1.0.0
 */
class LotteryGeneratorCLI
{
    /**
     * Generate Lotto numbers and output.
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     *
     * @param Route $route The ZF\Console\Route instance from the Dispatcher.
     * @param Console $console The Zend\Console adapter currently in use.
     */
    public function generateLotto(Route $route, Console $console)
    {
        $generator = new GenerateLotto();
        $result = $generator::generate();
        $ctr = 0;
        foreach ($result as $line) {
            $ctr++;
            $console->writeLine('Line ' . $ctr . ': ' . implode(', ', $line));
        }
    }
}