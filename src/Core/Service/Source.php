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
     * The organisation name to get the source for.
     *
     * @var string
     */
    private $organisation;

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
     * @param string $organisation
     * @param string $branch
     */
    public function __construct(Client $client, $organisation, $branch = 'develop')
    {
        $this->client = $client;
        $this->organisation = $organisation;
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
                $this->organisation,
                $repositoryName,
                $path,
                $this->branch
            );

        return $response;
    }
}
