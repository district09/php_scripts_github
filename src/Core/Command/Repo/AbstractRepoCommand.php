<?php

namespace DigipolisGent\Github\Core\Command\Repo;

use DigipolisGent\Github\Core\Filter\FilterSet;
use DigipolisGent\Github\Core\Filter\Pattern;
use DigipolisGent\Github\Core\Filter\Type;
use DigipolisGent\Github\Core\Command\AbstractCommand;
use DigipolisGent\Github\Core\Handler\RepositoryHandler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Abstract base class for commands.
 *
 * @package DigipolisGent\Github\Core\Command\Repo
 */
abstract class AbstractRepoCommand extends AbstractCommand
{
    /**
     * The repository handler.
     *
     * @var RepositoryHandler
     */
    private $handler;

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        parent::configure();

        // An organisation must be specified.
        $this->requireOrganisation();

        // Filter by regular expression.
        $this->addOption(
            'patterns',
            null,
            InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
            'Regular expression patterns to filter by name.'
        );

        // Filter by type.
        $this->addOption(
            'types',
            null,
            InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
            sprintf(
                'Repository type to filter by (%s)',
                implode(', ', Type::supportedTypes())
            )
        );
    }

    /**
     * Get the repository handler.
     *
     * @return RepositoryHandler
     */
    protected function getHandler()
    {
        if (null === $this->handler) {
            $this->handler = new RepositoryHandler($this->getClient());

            if ($this->logger) {
                $this->handler->setLogger($this->logger);
            }
        }

        return $this->handler;
    }

    /**
     * Get the organisation repositories that match the specified filters.
     *
     * @param InputInterface $input
     *   The input interface.
     *
     * @return array
     *   An array of repositories as returned by the GitHub API.
     *
     * @throws \Exception
     */
    protected function getRepositories(InputInterface $input)
    {
        return $this->getHandler()
            ->getByOrganisation(
                $this->getOrganisation($input),
                $this->getFilters($input)
            );
    }

    /**
     * Parse the specified filters.
     *
     * @param InputInterface $input
     *   The input interface.
     *
     * @return FilterSet
     */
    protected function getFilters($input)
    {
        $filters = new FilterSet();

        foreach (['patterns', 'types'] as $type) {
            if (!$this->isOptionSpecified($input, $type)) {
                continue;
            }

            $class = ($type === 'patterns' ? Pattern::class : Type::class);
            $patterns = new FilterSet(FilterSet::OPERATOR_OR);
            foreach ($input->getOption($type) as $filter) {
                $patterns->addFilter(new $class($filter));
            }

            $filters->addFilter($patterns);
        }

        return $filters;
    }
}
