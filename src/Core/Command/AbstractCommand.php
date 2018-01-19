<?php

namespace DigipolisGent\Github\Core\Command;

use DigipolisGent\Github\Core\Log\ConsoleLogger;
use Github\Client;
use Github\Exception\TwoFactorAuthenticationRequiredException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Abstract base class for commands.
 *
 * @package DigipolisGent\Github\Core\Command
 */
abstract class AbstractCommand extends Command
{
    /**
     * The output style.
     *
     * @var OutputInterface
     */
    protected $outputStyle;

    /**
     * The logger.
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * The Github Client.
     *
     * @var Client
     */
    private $client;

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        parent::configure();

        $this->addOption(
            'access-token',
            'u',
            InputOption::VALUE_REQUIRED,
            'Github personal access token (https://github.com/settings/tokens).'
        );
    }

    /**
     * Add the organisation argument.
     */
    protected function requireOrganisation()
    {
        $this->addArgument(
            'organisation',
            InputArgument::REQUIRED,
            'Name of the github organisation.'
        );
    }

    /**
     * @inheritdoc
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->outputStyle = new SymfonyStyle($input, $output);
        $this->logger = new ConsoleLogger($this->outputStyle);

        try {
            if ($this->isOptionSpecified($input, 'access-token')) {
                $this->authenticate($input);
            }
        } catch (InvalidOptionException $ex) {
            // Nothing to do.
        }
    }

    /**
     * Use authentication during the GitHub API requests.
     *
     * @param InputInterface $input
     *   The input interface.
     *
     * @throws InvalidOptionException
     */
    private function authenticate(InputInterface $input)
    {
        try {
            $this->getClient()
                ->authenticate(
                    $input->getOption('access-token'),
                    '',
                    Client::AUTH_HTTP_PASSWORD
                );
        } catch (TwoFactorAuthenticationRequiredException $e) {
            throw new InvalidOptionException(
                sprintf('Two factor authentication of type %s is required.', $e->getType())
            );
        }
    }

    /**
     * Get the Github client to use in the services.
     *
     * @return Client
     */
    protected function getClient()
    {
        if (null === $this->client) {
            $this->client = new Client();
        }

        return $this->client;
    }

    /**
     * Get the specified organisation.
     *
     * @param InputInterface $input
     *   The input interface.
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected function getOrganisation(InputInterface $input)
    {
        if (!$input->hasArgument('organisation')) {
            throw new InvalidArgumentException('The organisation argument does not exist.');
        }

        return $input->getArgument('organisation');
    }

    /**
     * Check if an option has been specified.
     *
     * @param InputInterface $input
     *   The input interface.
     * @param string $name
     *   The option name.
     *
     * @return bool
     *
     * @throws InvalidOptionException
     */
    protected function isOptionSpecified(InputInterface $input, $name)
    {
        if (!$input->hasOption($name)) {
            throw new InvalidOptionException(
                sprintf("The %s option doesn't exists.", $name)
            );
        }

        if ($input->hasParameterOption('--' . $name)) {
            return true;
        }

        $option = $this->getDefinition()->getOption($name);
        $name = $option->getShortcut();

        return null !== $name && $input->hasParameterOption('-' . $name);
    }
}
