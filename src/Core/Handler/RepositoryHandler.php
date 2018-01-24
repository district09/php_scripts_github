<?php

namespace DigipolisGent\Github\Core\Handler;

use DigipolisGent\Github\Core\Filter\FilterSet;

/**
 * Handler for repository API calls.
 *
 * @package DigipolisGent\Github\Core\Handler
 */
class RepositoryHandler extends HandlerAbstract
{
    /**
     * Get the repositories of an organisation.
     *
     * @param string $organisation
     *   The organisation name.
     * @param FilterSet $filters
     *   Set of filters that must match.
     *
     * @return array
     *   Array of repositories.
     *
     * @throws \Exception
     */
    public function getByOrganisation($organisation, FilterSet $filters = null)
    {
        $this->logVerbose('Retrieving repositories of %s...', $organisation);

        $repositories = [];
        $page = 1;

        do {
            $fetched = $this->client
                ->api('organization')
                ->repositories($organisation, 'all', $page);

            if (!$fetched) {
                break;
            }

            $repositories = array_merge($repositories, $fetched);

            $this->logVerbose(
                '  > Added %d repositories to list.',
                count($fetched)
            );
        } while ($page++);

        $this->logVerbose('Found %d repositories.', count($repositories));

        if ($filters && !$filters->isEmpty()) {
            $repositories = $this->filter($repositories, $filters);
        }

        return $repositories;
    }

    /**
     * Update a repository.
     *
     * @param string $owner
     *   The repository owner.
     * @param string $name
     *   The repository name.
     * @param array $values
     *   Array of values to post.
     *
     * @throws \Exception
     */
    public function update($owner, $name, array $values)
    {
        $this->logVerbose(
            'Updating repository %s/%s...',
            $owner,
            $name
        );

        if (!array_key_exists('name', $values)) {
            $values['name'] = $name;
        }

        $this->client->api('repo')->update($owner, $name, $values);
    }

    /**
     * Filter the repositories array.
     *
     * @param array $repositories
     *   Array of repositories as retrieved from GitHub.
     * @param FilterSet $filters
     *   Set of filters that must match.
     *
     * @return array
     *   The filtered repositories array.
     */
    protected function filter(array $repositories, FilterSet $filters)
    {
        $filtered = array_filter($repositories, function ($repository) use ($filters) {
            if ($filters->passes($repository['name'])) {
                return true;
            }

            return false;
        });

        $this->logVerbose(
            'Filtered %d out of %d repositories.',
            count($filtered),
            count($repositories)
        );

        return $filtered;
    }
}
