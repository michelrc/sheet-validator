<?php

namespace Payroll\Test\Log;

use \Psr\Log\AbstractLogger;


class DummyLogger extends AbstractLogger {

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
        echo "Dummy log: " . $message;
    }
}
