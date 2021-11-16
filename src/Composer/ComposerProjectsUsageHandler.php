<?php

namespace DigipolisGent\Github\Composer;

use DigipolisGent\Github\Composer\Composer;
use DigipolisGent\Github\Core\Handler\UsageHandlerAbstract;
use DigipolisGent\Github\Core\Project\Usage;

/**
 * Handler to find Composer project usages within repositories.
 *
 * @package DigipolisGent\Github\Composer
 */
class ComposerProjectsUsageHandler extends UsageHandlerAbstract
{

    /**
     * {@inheritdoc}
     */
    public function getUsageInRepository($repository, $project)
    {
        // Get the composer.lock file for the repository.
        $response = $this->service->raw($repository['name'], 'composer.lock');
        if (!array_key_exists('content', $response)) {
            return;
        }
        $composer = base64_decode($response['content']);

        $composerContent = Composer::fromRaw($composer);
        $projectInfo = $composerContent->searchProject($project);

        if ($projectInfo) {
            return new Usage($projectInfo, $repository['name']);
        }

        return false;
    }
}
