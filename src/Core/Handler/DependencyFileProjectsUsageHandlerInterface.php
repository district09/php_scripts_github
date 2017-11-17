<?php

namespace DigipolisGent\Github\Core\Handler;

use DigipolisGent\Github\Core\Service\Source;

/**
 * Interface for ProjectsUsageHandler imlementations for DependencyFiles
 */
interface DependencyFileProjectsUsageHandlerInterface
{
    /**
     * Find usages in a single repository.
     *
     * @param array $repository
     *   The repository information.
     * @param array $found
     *   The array to store the found results in.
     */
    public function getUsageInRepository($repository, array &$found);
}
