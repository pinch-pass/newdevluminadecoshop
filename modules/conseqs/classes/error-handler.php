<?php
/**
 * Copyright (C) 2017-2019 Petr Hucik <petr@getdatakick.com>
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the DataKick Regular License version 1.0
 * For more information see LICENSE.txt file
 *
 * @author    Petr Hucik <petr@getdatakick.com>
 * @copyright 2017-2019 Petr Hucik
 * @license   Licensed under the DataKick Regular License version 1.0
 */

namespace Conseqs;

use Exception;
use Logger;
use Db;

class ErrorHandler
{

    /** @var bool */
    private $registered;

    /** @var callable original error handler */
    private $orig;

    /** @var array */
    private $stack = [];


    /**
     * @param $name
     * @param callable $callable
     * @param array $parameters
     * @param array $tags
     * @return mixed
     */
    public function handleErrors($name, $callable, $parameters=[], $tags=[])
    {
        $this->setUp($name, $tags);
        try {
            return call_user_func_array($callable, $parameters);
        } catch(Exception $exception) {
            $this->logError($exception->getMessage(), $exception->getFile(), $exception->getLine(), $exception->getTraceAsString());
        } finally {
            $this->tearDown();
        }
        return false;
    }

    /**
     * @param string $name
     * @param array $tags
     */
    private function setUp($name, $tags)
    {
        if (!$this->registered) {
            $this->registered = true;
            register_shutdown_function(array($this, "onShutdown"));
        }

        if (! $this->orig) {
            $this->orig = set_error_handler(array($this, 'errorHandler'));
        }
        array_push($this->stack, [
            'name' => $name,
            'tags' => $tags

        ]);
    }

    /**
     *
     */
    private function tearDown()
    {
        array_pop($this->stack);
        if ($this->stack) {
            set_error_handler($this->orig);
            $this->orig = null;
        }
    }

    /**
     * @param $message
     * @param $file
     * @param $line
     * @param null $stacktrace
     */
    private function logError($message, $file, $line, $stacktrace=null)
    {
        try {
            $date = date('Ymd');
            $logFile = Utils::getLogDirectory() . "{$date}_conseqs-errors.log";
            $prefix = [];
            $tags = [];
            $date = date('Y-m-d H:i:s');
            for ($i = 0; $i < count($this->stack); $i++) {
                $entry = $this->stack[$i];
                $prefix[] = $entry['name'];
                $tags = array_merge($tags, $entry['tags']);
            }
            $conn = Db::getInstance();
            $payload = array_merge($tags, [
                'message' => pSQL($message),
                'file' => pSQL($file),
                'line' => (int)$line,
                'stacktrace' => $stacktrace ? pSQL($stacktrace) : null,
                'date' => $date,
            ]);
            $conn->insert('conseqs_errors', $payload);
            $prefix = "[" . implode(' > ', $prefix) . "]";
            $content = "$date $prefix $message in $file at line $line\n";
            if ($stacktrace) {
                $content .= $stacktrace . "\n";
            }
            @file_put_contents($logFile, $content, FILE_APPEND);
        } catch (\Exception $ignored) {
        }
    }

    /**
     * @param $errno
     * @param $errstr
     * @param $file
     * @param $line
     * @return bool
     */
    public function errorHandler($errno, $errstr, $file, $line)
    {
        if ($errno == E_USER_ERROR) {
            $this->logError($errstr, $file, $line);
        }
        return false;
    }

    /**
     *
     */
    public function onShutdown()
    {
        $error = error_get_last();
        if ($error && $error['type'] === E_ERROR) {
            $this->logError($error['message'],  $error['file'], $error['line']);
        }
    }

}
