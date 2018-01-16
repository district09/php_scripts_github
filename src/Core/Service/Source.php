<?php

namespace DigipolisGent\Github\Core\Service;

use Github\Client;

/**
 * GitHub service.
 *
 * @package DigipolisGent\Github\Core\Service
 */
class Source
{
    /**
     * The Github client.
     *
     * @var Client
     */
    private $client;

    /**
     * The team name to get the source for.
     *
     * @var string
     */
    private $team;

    /**
     * The branch to get the source for.
     *
     * @var string
     */
    private $branch;

    /**
     * Construct the service.
     *
     * @param Client $client
     * @param string $team
     * @param string $branch
     */
    public function __construct(Client $client, $team, $branch = 'develop')
    {
        $this->client = $client;
        $this->team = $team;
        $this->branch = $branch;
    }

    /**
     * Get raw content of given file.
     *
     * @param string $repositoryName
     * @param string $path
     *   Path to the file.
     *
     * @return string
     *   Raw content of the file.
     */
    public function raw($repositoryName, $path)
    {
        $response = $this
            ->client
            ->api('repo')
            ->contents()
            ->show(
                $this->team,
                $repositoryName,
                $path,
                $this->branch
            );

        return $response;
    }
}
