<?php

namespace DigipolisGent\Github\Core\Handler;

/**
 * Interface for ProjectsUsageHandler imlementations for DependencyFiles
 */
interface DependencyFileProjectsUsageHandlerInterface
{
    /**
     * Find usages of a single project in a single repository.
     *
     * @param string $repository
     *   The repository name.
     * @param string $project
     *   The project name to find.
     *
     * @return Usage|false
     */
    public function getUsageInRepository($repository, $project);
}
