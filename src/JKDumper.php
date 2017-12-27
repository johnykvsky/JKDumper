<?php

namespace johnykvsky\Utils;

use PHPUnit\Runner\Exception;


class JKDumper
{
    public $timingTasks;
    public static $instance;
    public static $sapi;
    public $logger;

    public static function instance()
    {
        if (!empty(self::$instance)) {
            return self::$instance;
        }

        self::$instance = new self();

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
        if (empty($this->logger) or !($this->logger instanceof \Psr\Log\AbstractLogger)) {
            return false;
        }

        return true;
    }

    public function log($var)
    {
        if (!$this->checkForLogger()) {
            return false;
        }

        $this->logger->debug(self::vdump($var));
        return true;
    }

    public static function getSapi()
    {
        if (self::$sapi === null) {
            self::$sapi = PHP_SAPI;
        }

        return self::$sapi;
    }

    public static function vdump($var, $echo = false)
    {
        if (!extension_loaded('xdebug')) {
            ob_start();
            $output = ob_get_clean();
        } else {
            $output = self::exportVar($var);
        }

        // neaten the newlines and indents
        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);

        if (self::getSapi() == 'cli') {
            $output = PHP_EOL
                    . PHP_EOL . $output
                    . PHP_EOL;
        } else {
            if (!extension_loaded('xdebug') and $echo) {
                $output = htmlspecialchars($output, ENT_QUOTES);
            }
        }

        if ($echo) {
            echo("<pre>".trim($output)."</pre>");
        }

        return trim($output);
    }

    public static function export($var, $depth, $indent, $varObject = null)
    {
        switch (self::getType($var)) {
            case 'boolean':
                return ($var) ? 'true' : 'false';
            case 'integer':
                return $var;

            case 'float':
                return $var;
            case 'string':
                if (trim($var) == '') {
                    return "''";
                }
                return "'" . $var . "'";
            case 'array':
                return self::array($var, $depth - 1, $indent + 1);
            case 'resource':
                $metadata = stream_get_meta_data($var);
                return '(resource) '.$metadata['uri'];
            case 'null':
                return 'null';

            default:
                return self::object($var, $depth - 1, $indent + 1, $varObject);
        }
    }

    public static function object($var, $depth, $indent, $prevObject = null)
    {
        $out = '';
        $props = array();

        if ($var == $prevObject) {
            return $out = '*RECURSION*';
        }

        $className = get_class($var);
        $out .= 'object(' . $className . ') {';

        if ($depth > 0) {
            $end = "\n" . str_repeat("    ", $indent - 1);
            $break = "\n" . str_repeat("    ", $indent);
            $objectVars = get_object_vars($var);

            foreach ($objectVars as $key => $value) {
                $value = self::export($value, $depth - 1, $indent, $var);
                $props[] = "$key => " . $value;
            }

            $out .= $break . implode($break, $props) . $end;
        }

        $out .= '}';

        return $out;
    }

    public static function array(array $var, $depth, $indent)
    {
        $out = "array (".count($var).") [";
        $n = $break = $end = null;

        if (!empty($var)) {
            $n = "\n";
            $break = "\n" . str_repeat("    ", $indent);
            $end = "\n" . str_repeat("    ", $indent - 1);
        }

        if ($depth >= 0) {
            foreach ($var as $key => $val) {
                if (json_encode($val) !== json_encode($var)) {
                    $val = self::export($val, $depth, $indent);
                } else {
                    $val = '[recursion]';
                }
                $vars[] = $break . self::exportVar($key) . ' => ' . $val;
            }
        } else {
            $vars[] = $break . '[maximum depth reached]';
        }

        return $out . implode(',', $vars) . $end . ']';
    }

    public static function getType($var)
    {
        if (is_object($var)) {
            return get_class($var);
        }

        if (is_null($var)) {
            return 'null';
        }

        if (is_string($var)) {
            return 'string';
        }

        if (is_array($var)) {
            return 'array';
        }

        if (is_int($var)) {
            return 'integer';
        }

        if (is_bool($var)) {
            return 'boolean';
        }

        if (is_float($var)) {
            return 'float';
        }

        if (is_resource($var)) {
            return 'resource';
        }

        return 'unknown';
    }

    public static function exportVar($var, $depth = 10)
    {
        return self::export($var, $depth, 0);
    }
}
