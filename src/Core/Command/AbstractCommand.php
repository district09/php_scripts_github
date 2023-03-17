<?php

namespace DigipolisGent\Github\Core\Command;

use DigipolisGent\Github\Core\Filter\FilterSet;
use DigipolisGent\Github\Core\Filter\Pattern;
use DigipolisGent\Github\Core\Filter\Type;
use DigipolisGent\Github\Core\Handler\RepositoryHandler;
use DigipolisGent\Github\Core\Input\ArgvInput;
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
     * The repository handler.
     *
     * @var RepositoryHandler
     */
    private $repoHandler;

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
     * Add the repository filters.
     *
     * @return self
     */
    protected function addRepositoryOptions()
    {
        // Filter by regular expression.
        $this->addOption(
            'pattern',
            null,
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Regular expression patterns to filter by name.'
        );

        // Filter by type.
        $this->addOption(
            'type',
            null,
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            sprintf(
                'Repository type to filter by (%s)',
                implode(', ', Type::supportedTypes())
            )
        );

        return $this;
    }

    /**
     * Add the organisation argument.
     *
     * @return self
     */
    protected function addOrganisationArgument()
    {
        $this->addArgument(
            'organisation',
            InputArgument::REQUIRED,
            'Name of the github organisation.'
        );

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->outputStyle = new SymfonyStyle($input, $output);
        $this->logger = new ConsoleLogger($this->outputStyle);

        try {
            $input = $this->convertInput($input);

            if ($input->isOptionSpecified('access-token')) {
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
     *   The input object.
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
                    \Github\AuthMethod::ACCESS_TOKEN
                );
        } catch (TwoFactorAuthenticationRequiredException $e) {
            throw new InvalidOptionException(
                sprintf('Two factor authentication of type %s is required.', $e->getType())
            );
        }
    }

    /**
     * Convert the default input object to our own class.
     *
     * @param InputInterface $input
     *   The input object.
     *
     * @return ArgvInput
     */
    protected function convertInput(InputInterface $input)
    {
        if ($input instanceof ArgvInput) {
            return $input;
        }

        return new ArgvInput($this->getDefinition(), $input);
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
     * Get the repository handler.
     *
     * @return RepositoryHandler
     */
    protected function getRepositoryHandler()
    {
        if (null === $this->repoHandler) {
            $this->repoHandler = new RepositoryHandler($this->getClient());

            if ($this->logger) {
                $this->repoHandler->setLogger($this->logger);
            }
        }

        return $this->repoHandler;
    }

    /**
     * Get the specified organisation.
     *
     * @param InputInterface $input
     *   The input object.
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
     * Parse the specified filters.
     *
     * @param InputInterface $input
     *   The input object.
     *
     * @return FilterSet
     */
    protected function getRepositoryFilters(InputInterface $input)
    {
        $input = $this->convertInput($input);
        $filters = new FilterSet();

        foreach (['pattern', 'type'] as $type) {
            if (!$input->isOptionSpecified($type)) {
                continue;
            }

            $class = ($type === 'pattern' ? Pattern::class : Type::class);
            $patterns = new FilterSet(FilterSet::OPERATOR_OR);
            foreach ($input->getOption($type) as $filter) {
                $patterns->addFilter(new $class($filter));
            }

            $filters->addFilter($patterns);
        }

        return $filters;
    }

    /**
     * Get the organisation repositories that match the specified filters.
     *
     * @param InputInterface $input
     *   The input object.
     *
     * @return array
     *   An array of repositories as returned by the GitHub API.
     *
     * @throws \Exception
     */
    protected function getRepositories(InputInterface $input)
    {
        return $this->getRepositoryHandler()
            ->getByOrganisation(
                $this->getOrganisation($input),
                $this->getRepositoryFilters($input)
            );
    }
}
