<?php

namespace DigipolisGent\Github\Core\Service;

use Github\Client;
use Github\Exception\RuntimeException;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Contracts\Cache\CacheInterface;

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
     * @var CacheInterface
     */
    protected $cache;

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
        $this->disableCache();
    }

    public function disableCache()
    {
        $this->cache = new NullAdapter();
    }

    public function enableCache()
    {
        $this->cache = new ArrayAdapter();
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
        $key = $repositoryName . ':' . $path;
        try {
            $response = $this->cache->get($key, function () use ($repositoryName, $path) {
                return $this
                    ->client
                    ->api('repo')
                    ->contents()
                    ->show(
                        $this->organisation,
                        $repositoryName,
                        $path,
                        $this->branch
                    );
            });

        } catch (RuntimeException $e) {
            // Do nothing if exception was thrown.
            return [];
        }

        return $response;
    }
}
