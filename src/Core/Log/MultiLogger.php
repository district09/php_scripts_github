<?php

namespace DigipolisGent\Github\Core\Log;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Container for multiple loggers.
 *
 * Calling a log method will call the method on al registered loggers.
 *
 * @package DigipolisGent\Github\Core\Logger
 */
class MultiLogger implements LoggerInterface
{
    /**
     * Loggers.
     *
     * @var LoggerInterface[]
     */
    private $loggers = [];

    /**
     * Constructor.
     *
     * @param LoggerInterface[] $loggers
     */
    public function __construct(array $loggers = [])
    {
        foreach ($loggers as $logger) {
            $this->addLogger($logger);
        }
    }

    /**
     * Add a logger to multi logger.
     *
     * @param LoggerInterface $logger
     */
    public function addLogger(LoggerInterface $logger)
    {
        $this->loggers[] = $logger;
    }

    /**
     * @inheritDoc
     */
    public function emergency($message, array $context = [])
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function alert($message, array $context = [])
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function critical($message, array $context = [])
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function error($message, array $context = [])
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function warning($message, array $context = [])
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function notice($message, array $context = [])
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function info($message, array $context = [])
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function debug($message, array $context = [])
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function log($level, $message, array $context = [])
    {
        foreach ($this->loggers as $logger) {
            $logger->log($level, $message, $context);
        }
    }
}
