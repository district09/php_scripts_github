<?php

namespace DigipolisGent\Github\Core\Handler;

use Github\Client;

/**
 * Handler to get all repositories for the provided team name.
 *
 * @package DigipolisGent\Github\Core\Handler
 */
class RepositoriesHandler extends HandlerAbstract
{
    /**
     * The Github Client to get the repositories from.
     *
     * @var Client
     */
    private $client;

    /**
     * Constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get a list of repositories for the given organisation.
     *
     * @param string $team
     *   The Github team name to get the repositories for.
     *
     * @return array
     *   Array of repositories info.
     */
    public function getRepositories($team, $type = 'all')
    {
        $this->logVerbose(
            'Retrieving repositories information for %s...',
            $team
        );

        $repositories = [];
        $page = 1;

        // Load the paged content.
        do {
            $repositoriesLast = $this->client->api('organization')->repositories($team, $type, $page);
            $repositories = array_merge($repositoriesLast, $repositories);

            $this->logVerbose(
                '  > Added %d repositories to repositories list.',
                count($repositoriesLast)
            );

            $page++;
        } while (count($repositoriesLast) > 0);

        $this->logVerbose(
            'Found %d repositories.',
            count($repositories)
        );

        return $repositories;
    }
}
