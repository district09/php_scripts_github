<?php

namespace DigipolisGent\Github\MakeFile;

use DigipolisGent\Github\Core\Handler\DependencyFileProjectsUsageHandlerInterface;
use DigipolisGent\Github\Core\Handler\HandlerAbstract;
use DigipolisGent\Github\Core\Service\Source;

/**
 * Handler to find MakeFile project usages within repositories.
 *
 * @package DigipolisGent\Github\MakeFile
 */
class MakeFileProjectsUsageHandler extends HandlerAbstract implements DependencyFileProjectsUsageHandlerInterface
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

        // Get the make file for the repository.
        $response = $this->service->raw($repository['name'], 'build.make.yml');
        if (!array_key_exists('content', $response)) {
            return;
        }
        $makeFile = base64_decode($response['content']);

        foreach ($projects as $project) {
            if ($this->getUsageInRepositoryAsBrick($project, $makeFile)) {
                $found[$project]['brick'][] = $repository['name'];
                continue;
            }
            if ($this->getUsageInRepositoryAsCustom($project, $repository)) {
                $found[$project]['custom'][] = $repository['name'];
                continue;
            }
        }
    }

    /**
     * Find project as brick.
     *
     * @param string $projectName
     *   The project name to search for.
     * @param string $makeFile.
     *   The make file content.
     *
     * @return bool
     *   Found.
     */
    protected function getUsageInRepositoryAsBrick($projectName, $makeFile)
    {
        $makeContent = MakeFile::fromRaw($makeFile);
        $project = $makeContent->searchProject($projectName);
        if (!$project) {
            return false;
        }

        $this->logVerbose(
            '  > Found project %s as brick:',
            $projectName
        );
        $this->logVerbose(
            '    - Version : %s',
            $project->getVersion()
        );
        $this->logVerbose(
            '    - SourceType : %s',
            $project->getSourceType()
        );
        $this->logVerbose(
            '    - SourceUrl : %s',
            $project->getSourceUrl()
        );
        return true;
    }

    /**
     * Search a module used as a custom module within a repository.
     *
     * The module/theme is searched by its info file within:
     *   - custom/modules/custom/[module_name]/[module_name].info
     *   - custom/modules/features/[module_name]/[module_name].info
     *   - custom/themes/custom/[theme_name]/[theme_name].info
     *
     * @param string $project
     *   The module or theme name.
     * @param array $repository
     *   The repository to search in.
     *
     * @return bool
     *   Found.
     */
    protected function getUsageInRepositoryAsCustom($project, $repository)
    {
        if ($this->getProfileUsageInRepositoryAsCustom($project, $repository)) {
            $this->logVerbose(
                '  > Found %s as custom profile.',
                $project
            );
            return true;
        }
        if ($this->getModuleUsageInRepositoryAsCustom($project, $repository)) {
            $this->logVerbose(
                '  > Found %s as custom module.',
                $project
            );
            return true;
        }
        if ($this->getFeatureUsageInRepositoryAsCustom($project, $repository)) {
            $this->logVerbose(
                '  > Found %s as custom feature.',
                $project
            );
            return true;
        }
        if ($this->getThemeUsageInRepositoryAsCustom($project, $repository)) {
            $this->logVerbose(
                '  > Found %s as custom theme.',
                $project
            );
            return true;
        }

        return false;
    }

    /**
     * Search a profile within a repository as a custom profile.
     *
     * @param string $profile
     *   The profile name.
     * @param array $repository
     *   The repository to search in.
     *
     * @return bool
     *   Found.
     */
    public function getProfileUsageInRepositoryAsCustom($profile, $repository)
    {
        $response = $this->service->raw(
            $repository['name'],
            'custom/profile/' . $profile . '/' . $profile . 'info'
        );

        return array_key_exists('content', $response);
    }

    /**
     * Search a module within a repository as a custom module.
     *
     * @param string $module
     *   The module or theme name.
     * @param array $repository
     *   The repository to search in.
     *
     * @return bool
     *   Found.
     */
    public function getModuleUsageInRepositoryAsCustom($module, $repository)
    {
        $response = $this->service->raw(
            $repository['name'],
            "custom/modules/custom/{$module}/{$module}.info"
        );

        return array_key_exists('content', $response);
    }

    /**
     * Search a module within a repository as a custom feature.
     *
     * @param string $feature
     *   The module or theme name.
     * @param array $repository
     *   The repository to search in.
     *
     * @return bool
     *   Found.
     */
    public function getFeatureUsageInRepositoryAsCustom($feature, $repository)
    {
        $response = $this->service->raw(
            $repository['name'],
            "custom/modules/features/{$feature}/{$feature}.info"
        );

        return array_key_exists('content', $response);
    }

    /**
     * Search a theme within a repository as a custom theme.
     *
     * @param string $theme
     *   The module or theme name.
     * @param array $repository
     *   The repository to search in.
     *
     * @return bool
     *   Found.
     */
    public function getThemeUsageInRepositoryAsCustom($theme, $repository)
    {
        $response = $this->service->raw(
            $repository['name'],
            "custom/themes/{$theme}/{$theme}.info"
        );

        return array_key_exists('content', $response);
    }
}
