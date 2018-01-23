<?php

namespace johnykvsky\Utils;

use \Psr\Log\LoggerInterface;

class JKDumper
{
    public $timingTasks;
    public static $instance;
    public $logger;

    public static function instance()
    {
        if (!(self::$instance instanceof self)) 
        {
            self::$instance = new self;
        }
        
        return self::$instance;
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    public function startTime($task = "debug")
    {
        $this->timingTasks[$task] = microtime(true);

        if ($this->checkForLogger()) {
            $this->logger->info(sprintf("Started timing %s", $task));
        }

        return $this->timingTasks[$task];
    }

    public function endTime($task = "debug")
    {
        if (!array_key_exists($task, $this->timingTasks)) {
            if ($this->checkForLogger()) {
                $this->logger->info('ERROR. Task has not been started: '.$task);
            }
            return 0;
        }

        $startTime = $this->timingTasks[$task];

        if (isset($startTime)) {
            $endTime = microtime(true) - $startTime;
            //convert to millseconds (most common)
            $endTime *= 1000;

            if ($this->checkForLogger()) {
                $this->logger->info(sprintf("Finished %s in %.3f milliseconds", $task, $endTime));
            }

            return $endTime;
        }

        return 0;
    }

    public function checkForLogger()
    {
        if (empty($this->logger) or !($this->logger instanceof LoggerInterface)) {
            return false;
        }

        return true;
    }

    public function log($var)
    {
        if (!$this->checkForLogger()) {
            return false;
        }

        $this->logger->debug($this->vdump($var));
        return true;
    }

    public function vdump($var, $echo = false)
    {
        if (extension_loaded('xdebug')) {
            $xd_ovd = ini_get("xdebug.overload_var_dump");
            //we need to disable xdebug pretty dumping
            ini_set("xdebug.overload_var_dump", 0);
        }

        ob_start();
        var_dump($var);
        $output = ob_get_clean();

        // neaten the newlines and indents
        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", trim($output));

        if ($echo) {
            if (in_array(PHP_SAPI, array('cli', 'cli-server', 'phpdbg')) || substr(PHP_SAPI, 0, 3) == 'cgi') {
                echo(PHP_EOL . $output . PHP_EOL);
            } else {
                echo("<pre>".$output."</pre>");
            }
        }

        if (isset($xd_ovd)) {
            //lets get back xdebug pretty dumping state
            ini_set("xdebug.overload_var_dump", $xd_ovd);
        }

        return $output;
    }
}
