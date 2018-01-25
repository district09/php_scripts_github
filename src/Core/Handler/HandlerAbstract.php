<?php

namespace DigipolisGent\Github\Core\Handler;

use Github\Client;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Abstract base class for the handlers.
 *
 * @package DigipolisGent\Github\Core\Handler
 */
abstract class HandlerAbstract implements LoggerAwareInterface
{
    /**
     * GitHub client.
     *
     * @var Client
     */
    protected $client;

    /**
     * Logger instance.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Class constructor.
     *
     * @param Client $client
     *   The GitHub client.
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @inheritDoc
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Log a message.
     *
     * @param int $level
     *   The log level to log the message for.
     * @param string $message
     *   The message to log.
     * @param array $context
     *   The log context.
     */
    protected function log($level, $message, array $context = [])
    {
        if (!$this->logger) {
            return;
        }

        $this->logger->log($level, $message, $context);
    }

    /**
     * Log a verbose message.
     *
     * Pass extra arguments to the method to add replacements for the $message
     * string placeholders.
     *
     * @param string $message
     *   The message to log.
     */
    protected function logVerbose($message)
    {
        if (!$this->logger) {
            return;
        }

        // Get optional arguments.
        $args = func_get_args();
        array_shift($args);
        if (!empty($args)) {
            $message = vsprintf(
                $message,
                $args
            );
        }

        // Log the message using the available logger.
        $this->log(
            LogLevel::INFO,
            $message
        );
    }
}
