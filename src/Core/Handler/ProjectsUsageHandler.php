<?php

namespace DigipolisGent\Github\Core\Handler;

use DigipolisGent\Github\Core\Service\Source;

/**
 * Handler to find project usages within repositories.
 *
 * @package DigipolisGent\Github\Core\Handler
 */
class ProjectsUsageHandler extends HandlerAbstract
{
    /**
     * A mapping between repository prefixes and ProjectsUsageHandlers
     *
     * @var array
     */
    private $usageHandlerMapping = array(
        'drupal_site' => 'DigipolisGent\Github\MakeFile\MakeFileProjectsUsageHandler',
        'drupal8_site' => 'DigipolisGent\Github\Composer\ComposerProjectsUsageHandler',
        'php_package' => 'DigipolisGent\Github\Composer\ComposerProjectsUsageHandler',
        'symfony_site' => 'DigipolisGent\Github\Composer\ComposerProjectsUsageHandler',
        'laravel_site' => 'DigipolisGent\Github\Composer\ComposerProjectsUsageHandler',
        'generic_site' => 'DigipolisGent\Github\Composer\ComposerProjectsUsageHandler',
    );

    /**
     * The source service to use.
     *
     * @var Source
     */
    private $service;

    /**
     * The projects to search for.
     *
     * @var array
     */
    private $projects = [];

    /**
     * Construct the handler.
     *
     * @param Source $service
     * @param array $projects
     */
    public function __construct(Source $service, array $projects)
    {
        $this->service = $service;
        $this->projects = $projects;
    }

    /**
     * Search for usages.
     *
     * @param array $repositories
     *   (Filtered) array of repositories to search in.
     *
     * @return array
     *   Array containing the usage information.
     */
    public function getUsages(array $repositories)
    {
        $found = array_fill_keys(
            $this->projects,
            []
        );

        foreach ($repositories as $repository) {
            // Give feedback which repository we are looking in.
            $this->logVerbose(
                'Looking inside repository %s...',
                $repository['name']
            );

            $this->getUsageInRepository($repository, $found);
        }

        return $found;
    }

    /**
     * Find usages in a single repository.
     *
     * @param array $repository
     *   The repository information.
     * @param array $found
     *   The array to store the found results in.
     */
    protected function getUsageInRepository($repository, array &$found)
    {
        $mapping_found = false;
        foreach ($this->usageHandlerMapping as $repository_prefix => $class) {
            if (substr($repository['name'], 0, strlen($repository_prefix)) === $repository_prefix) {
                $mapping_found = true;
                $this->logVerbose(
                    'Using ProjectsUsageHandler %s.',
                    $class
                );

                $handler = new $class($this->service, $this->projects);
                $handler->getUsageInRepository($repository, $found);
            }
        }

        if (!$mapping_found) {
            $this->logVerbose(
                'No ProjectsUsageHandler found for %s.',
                $repository['name']
            );
        }
    }
}
