<?php

namespace Payroll\Test\Log;

use \Psr\Log\AbstractLogger;

/**
 * Class DummyLogger
 *
 * Used for testing purposes
 *
 * @package Payroll\Test\Log
 */
class DummyLogger extends AbstractLogger {

    private $messages = array();

    public function setMessages($messages)
    {
        $this->messages = $messages;
    }

    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        $this->messages[] = $message;
    }
}
