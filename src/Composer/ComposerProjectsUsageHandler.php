<?php

namespace DigipolisGent\Github\Composer;

use DigipolisGent\Github\Core\Handler\DependencyFileProjectsUsageHandlerInterface;
use DigipolisGent\Github\Core\Handler\HandlerAbstract;
use DigipolisGent\Github\Core\Service\Source;

/**
 * Handler to find Composer project usages within repositories.
 *
 * @package DigipolisGent\Github\Composer
 */
class ComposerProjectsUsageHandler extends HandlerAbstract implements DependencyFileProjectsUsageHandlerInterface
{
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
     * Find usages in a single repository.
     *
     * @param array $repository
     *   The repository information.
     * @param array $found
     *   The array to store the found results in.
     */
    public function getUsageInRepository($repository, array &$found)
    {
        $projects = $this->projects;

        // Get the composer.lock file for the repository.
        $response = $this->service->raw($repository['name'], 'composer.lock');
        $composer = $response->getContent();

        foreach ($projects as $projectName) {
            $composerContent = Composer::fromRaw($composer);
            $project = $composerContent->searchProject($projectName);

            if ($project) {
                $found[$projectName][] = $repository['name'];
                continue;
            }
        }
    }
}
