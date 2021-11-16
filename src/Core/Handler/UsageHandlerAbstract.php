<?php

namespace DigipolisGent\Github\Core\Handler;

use DigipolisGent\Github\Core\Handler\DependencyFileProjectsUsageHandlerInterface;
use DigipolisGent\Github\Core\Project\Usage;
use DigipolisGent\Github\Core\Service\Source;

/**
 * Abstract base class for the usage handlers.
 *
 * @package DigipolisGent\Github\Core\Handler
 */
abstract class UsageHandlerAbstract extends HandlerAbstract implements DependencyFileProjectsUsageHandlerInterface
{
    /**
     * The source service to use.
     *
     * @var Source
     */
    protected $service;

    /**
     * The projects to search for.
     *
     * @var array
     */
    protected $projects = [];

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
     * @param array $repositories
     *   Repositories to search in.
     *
     * @return Usage[]
     */
    public function getUsages(array $repositories) {
        $usages = [];
        foreach ($repositories as $repository) {
            foreach ($this->projects as $project) {
                if ($usage = $this->getUsageInRepository($repository, $project)) {
                    $usages[] = $usage;
                }
            }
        }

        return $usages;
    }
}
