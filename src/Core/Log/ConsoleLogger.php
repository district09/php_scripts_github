<?php

namespace DigipolisGent\Github\Core\Log;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Logger who outputs info to screen.
 *
 * @package DigipolisGent\Github\Core\Logger
 */
class ConsoleLogger implements LoggerInterface
{
    /**
     * Symfony Console Style.
     *
     * @var SymfonyStyle
     */
    private $io;

    /**
     * Constructor.
     *
     * @param SymfonyStyle $io
     */
    public function __construct(SymfonyStyle $io)
    {
        $this->io = $io;
    }

    /**
     * @inheritDoc
     */
    public function emergency($message, array $context = array())
    {
        $this->io->error(sprintf('Emergency : %s', $message));
    }

    /**
     * @inheritDoc
     */
    public function alert($message, array $context = array())
    {
        $this->io->error(sprintf('Alert : %s', $message));
    }

    /**
     * @inheritDoc
     */
    public function critical($message, array $context = array())
    {
        $this->io->error(sprintf('Critical : %s', $message));
    }

    /**
     * @inheritDoc
     */
    public function error($message, array $context = array())
    {
        $this->io->error(sprintf('Error : %s', $message));
    }

    /**
     * @inheritDoc
     */
    public function warning($message, array $context = array())
    {
        $this->io->warning(sprintf('Warning : %s', $message));
    }

    /**
     * @inheritDoc
     */
    public function notice($message, array $context = array())
    {
        $this->io->note(sprintf('%s', $message));
    }

    /**
     * @inheritDoc
     */
    public function info($message, array $context = array())
    {
        if (!$this->io->isVerbose()) {
            return;
        }

        $this->io->text(sprintf('%s', $message));
    }

    /**
     * @inheritDoc
     */
    public function debug($message, array $context = array())
    {
        if (!$this->io->isVeryVerbose()) {
            return;
        }

        $this->io->text(sprintf('%s', $message));
    }

    /**
     * @inheritDoc
     */
    public function log($level, $message, array $context = array())
    {
        if ($level === LogLevel::EMERGENCY) {
            return $this->emergency($message, $context);
        }
        if ($level === LogLevel::ALERT) {
            return $this->alert($message, $context);
        }
        if ($level === LogLevel::CRITICAL) {
            return $this->critical($message, $context);
        }
        if ($level === LogLevel::ERROR) {
            return $this->error($message, $context);
        }
        if ($level === LogLevel::WARNING) {
            return $this->warning($message, $context);
        }
        if ($level === LogLevel::NOTICE) {
            return $this->notice($message, $context);
        }
        if ($level === LogLevel::INFO) {
            return $this->info($message, $context);
        }
        if ($level === LogLevel::DEBUG) {
            return $this->debug($message, $context);
        }
    }
}
