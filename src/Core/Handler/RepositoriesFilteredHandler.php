<?php

namespace DigipolisGent\Github\Core\Handler;

use DigipolisGent\Github\Core\Filter\FilterInterface;
use Github\Client;

/**
 * Handler to get all repositories filtered by provided filters.
 *
 * @package DigipolisGent\Github\Core\Handler
 */
class RepositoriesFilteredHandler extends RepositoriesHandler
{
    /**
     * The filters to filter the list by.
     *
     * @var FilterInterface[]
     */
    private $filters = [];

    /**
     * Constructor.
     *
     * @param Client $client
     * @param FilterInterface[] $filters
     */
    public function __construct(Client $client, array $filters)
    {
        parent::__construct($client);
        $this->filters = $filters;
    }

    /**
     * @inheritdoc
     */
    public function getRepositories($team, $type = 'all')
    {
        $repositories = parent::getRepositories($team);
        return $this->filter($repositories);
    }

    /**
     * Filter the repositories list.
     *
     * @param array $repositories
     *
     * @return array
     */
    protected function filter(array $repositories)
    {
        $filtered = [];

        foreach ($repositories as $repository) {
            if (!$this->matchFilters($repository)) {
                continue;
            }

            $filtered[] = $repository;
        }

        $this->logVerbose(
            'Filtered %d out of %d repositories.',
            count($filtered),
            count($repositories)
        );

        return $filtered;
    }

    /**
     * Does the repository matches one of the filters.
     *
     * @param array $repository
     *
     * @return bool
     */
    protected function matchFilters($repository)
    {
        $name = $repository['name'];

        foreach ($this->filters as $filter) {
            if (!$filter->passes($name)) {
                continue;
            }

            return true;
        }

        return false;
    }
}
