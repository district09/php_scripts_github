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
    public function emergency($message, array $context = [])
    {
        $this->io->error(sprintf('Emergency : %s', $message));
    }

    /**
     * @inheritDoc
     */
    public function alert($message, array $context = [])
    {
        $this->io->error(sprintf('Alert : %s', $message));
    }

    /**
     * @inheritDoc
     */
    public function critical($message, array $context = [])
    {
        $this->io->error(sprintf('Critical : %s', $message));
    }

    /**
     * @inheritDoc
     */
    public function error($message, array $context = [])
    {
        $this->io->error(sprintf('Error : %s', $message));
    }

    /**
     * @inheritDoc
     */
    public function warning($message, array $context = [])
    {
        $this->io->warning(sprintf('Warning : %s', $message));
    }

    /**
     * @inheritDoc
     */
    public function notice($message, array $context = [])
    {
        $this->io->note(sprintf('%s', $message));
    }

    /**
     * @inheritDoc
     */
    public function info($message, array $context = [])
    {
        if (!$this->io->isVerbose()) {
            return;
        }

        $this->io->text(sprintf('%s', $message));
    }

    /**
     * @inheritDoc
     */
    public function debug($message, array $context = [])
    {
        if (!$this->io->isVeryVerbose()) {
            return;
        }

        $this->io->text(sprintf('%s', $message));
    }

    /**
     * @inheritDoc
     */
    public function log($level, $message, array $context = [])
    {
        switch ($level) {
            case LogLevel::EMERGENCY:
                $this->emergency($message, $context);
                break;

            case LogLevel::ALERT:
                $this->alert($message, $context);
                break;

            case LogLevel::CRITICAL:
                $this->critical($message, $context);
                break;

            case LogLevel::ERROR:
                $this->error($message, $context);
                break;

            case LogLevel::WARNING:
                $this->warning($message, $context);
                break;

            case LogLevel::NOTICE:
                $this->notice($message, $context);
                break;

            case LogLevel::INFO:
                $this->info($message, $context);
                break;

            case LogLevel::DEBUG:
                $this->debug($message, $context);
                break;
        }
    }
}
