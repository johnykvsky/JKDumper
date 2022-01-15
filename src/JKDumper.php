<?php

declare(strict_types=1);

namespace johnykvsky\Utils;

use Exception;
use Psr\Log\LoggerInterface;

class JKDumper
{
    public static ?self $instance = null;

    private array $timingTasks;
    private ?LoggerInterface $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->timingTasks = [];
        $this->logger = $logger;
    }

    public static function instance(?LoggerInterface $logger = null): self
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self($logger);
        }

        return self::$instance;
    }

    public function startTime(string $task = "debug"): float
    {
        $this->timingTasks[$task] = microtime(true);
        $this->logMessage(sprintf("Started timing %s", $task));

        return $this->timingTasks[$task];
    }

    public function loggingEnabled(): bool
    {
        return $this->logger instanceof LoggerInterface;
    }

    public function endTime(string $task = "debug"): float
    {
        if (empty($this->timingTasks)) {
            $this->logMessage('ERROR. Empty timingTasks for task: ' . $task);
            return 0;
        }

        if (!array_key_exists($task, $this->timingTasks)) {
            $this->logMessage('ERROR. Task has not been started: ' . $task);
            return 0;
        }

        $startTime = $this->timingTasks[$task];

        $endTime = microtime(true) - $startTime;
        //convert to milliseconds
        $endTime *= 1000;

        $this->logMessage(sprintf("Finished %s in %.3f milliseconds", $task, $endTime));

        return $endTime;
    }

    public function logVar(mixed $var): void
    {
        if (!$this->loggingEnabled()) {
            return;
        }

        if ($result = $this->vdump($var)) {
            $this->logger->debug($result);
        }
    }

    public function logMessage(string $message): void
    {
        if (!$this->loggingEnabled()) {
            return;
        }

        $this->logger->info($message);
    }

    public function vdump(mixed $var, bool $echo = false): ?string
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
            $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", trim((string) $output));

            if ($echo) {
                $this->echoData($output);
            }
            /* @phpstan-ignore-next-line */
        } catch (Exception $e) {
            $this->logMessage(sprintf("ERROR: JKDumper error: %s", $e->getMessage()));
        }

        if (isset($xd_ovd)) {
            //lets get back xdebug pretty dumping state
            ini_set('xdebug.overload_var_dump', (string) $xd_ovd);
        }

        return $output ?? null;
    }

    private function echoData(string $output): void
    {
        if (in_array(PHP_SAPI, array('cli', 'cli-server', 'phpdbg')) || str_starts_with(PHP_SAPI, 'cgi')) {
            echo(PHP_EOL . $output . PHP_EOL);
            return;
        }

        echo("<pre>" . $output . "</pre>");
    }
}
