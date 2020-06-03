<?php

declare(strict_types=1);

namespace johnykvsky\Utils;

use Exception;
use Psr\Log\LoggerInterface;

class JKDumper
{
    /** @var self $instance */
    public static $instance;
    /** @var float[] $timingTasks */
    public $timingTasks;
    /** @var LoggerInterface $logger */
    public $logger;

    /**
     * @return self
     */
    public static function instance(): self
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * @param LoggerInterface $logger
     * @return void
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     *
     * @param string $task
     * @return float
     */
    public function startTime(string $task = "debug"): float
    {
        $this->timingTasks[$task] = microtime(true);

        if ($this->checkForLogger()) {
            $this->logger->info(sprintf("Started timing %s", $task));
        }

        return $this->timingTasks[$task];
    }

    /**
     * @return bool
     */
    public function checkForLogger(): bool
    {
        if ($this->logger && $this->logger instanceof LoggerInterface) {
            return true;
        }

        return false;
    }

    /**
     * @param string $task
     * @return float
     */
    public function endTime(string $task = "debug"): float
    {
        if (!is_array($this->timingTasks)) {
            if ($this->checkForLogger()) {
                $this->logger->info('ERROR. Empty timingTasks for task: ' . $task);
            }
            return 0;
        }

        if (!array_key_exists($task, $this->timingTasks)) {
            if ($this->checkForLogger()) {
                $this->logger->info('ERROR. Task has not been started: ' . $task);
            }
            return 0;
        }

        $startTime = $this->timingTasks[$task];

        $endTime = microtime(true) - $startTime;
        //convert to millseconds (most common)
        $endTime *= 1000;

        if ($this->checkForLogger()) {
            $this->logger->info(sprintf("Finished %s in %.3f milliseconds", $task, $endTime));
        }

        return $endTime;
    }

    /**
     * @param mixed $var
     * @return bool
     */
    public function log($var): bool
    {
        if (!$this->checkForLogger()) {
            return false;
        }

        $result = $this->vdump($var);

        if ($result) {
            $this->logger->debug($result);
        }
        return true;
    }

    /**
     * @param mixed $var
     * @param boolean $echo
     * @return ?string
     */
    public function vdump($var, bool $echo = false): ?string
    {
        if (extension_loaded('xdebug')) {
            $xd_ovd = ini_get('xdebug.overload_var_dump');
            //we need to disable xdebug pretty dumping
            ini_set('xdebug.overload_var_dump', '0');
        }

        try {
            ob_start();
            var_dump($var);
            $output = ob_get_clean();

            // neaten the newlines and indents
            $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", trim($output));

            if ($echo) {
                $this->echoData($output);
            }
        } catch (Exception $e) {
            if (isset($xd_ovd)) {
                //lets get back xdebug pretty dumping state
                ini_set('xdebug.overload_var_dump', $xd_ovd);
            }
            throw $e;
        }

        return $output;
    }

    /**
     * @param string $output
     * @return void
     */
    private function echoData(string $output): void
    {
        if (in_array(PHP_SAPI, array('cli', 'cli-server', 'phpdbg'))
            || substr(PHP_SAPI, 0, 3) === 'cgi')
        {
            echo(PHP_EOL . $output . PHP_EOL);
        } else {
            echo("<pre>" . $output . "</pre>");
        }
    }
}
