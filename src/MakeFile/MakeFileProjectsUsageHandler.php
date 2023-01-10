<?php

namespace DigipolisGent\Github\MakeFile;

use DigipolisGent\Github\Core\Handler\DependencyFileProjectsUsageHandlerInterface;
use DigipolisGent\Github\Core\Handler\UsageHandlerAbstract;
use DigipolisGent\Github\Core\Project\ProjectInterface;
use DigipolisGent\Github\Core\Project\Usage;
use DigipolisGent\Github\MakeFile\Project\MakeFileUsage;

/**
 * Handler to find MakeFile project usages within repositories.
 *
 * @package DigipolisGent\Github\MakeFile
 */
class MakeFileProjectsUsageHandler extends UsageHandlerAbstract implements DependencyFileProjectsUsageHandlerInterface
{

    /**
     * {@inheritdoc}
     */
    public function getUsageInRepository($repository, $project)
    {

        // Get the make file for the repository.
        $response = $this->service->raw($repository['name'], 'build.make.yml');
        if (!array_key_exists('content', $response)) {
            return;
        }
        $makeFile = base64_decode($response['content']);

        if ($projectInfo = $this->getUsageInRepositoryAsBrick($project, $makeFile)) {
            return new MakeFileUsage($projectInfo, $repository['name'], MakeFileUsage::USAGE_BRICK);
        }
        if ($projectInfo = $this->getUsageInRepositoryAsCustom($project, $repository)) {
            return new MakeFileUsage($projectInfo, $repository['name'], MakeFileUsage::USAGE_CUSTOM);
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
     * @return ProjectInterface|false
     */
    protected function getUsageInRepositoryAsBrick($projectName, $makeFile)
    {
        $makeContent = MakeFile::fromRaw($makeFile);
        return $makeContent->searchProject($projectName);
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
     * @return ProjectInterface|false
     */
    protected function getUsageInRepositoryAsCustom($project, $repository)
    {
        if ($projectInfo = $this->getProfileUsageInRepositoryAsCustom($project, $repository)) {
            $this->logVerbose(
                '  > Found %s as custom profile.',
                $project
            );
            return $projectInfo;
        }
        if ($projectInfo = $this->getModuleUsageInRepositoryAsCustom($project, $repository)) {
            $this->logVerbose(
                '  > Found %s as custom module.',
                $project
            );
            return $projectInfo;
        }
        if ($projectInfo = $this->getFeatureUsageInRepositoryAsCustom($project, $repository)) {
            $this->logVerbose(
                '  > Found %s as custom feature.',
                $project
            );
            return $projectInfo;
        }
        if ($projectInfo = $this->getThemeUsageInRepositoryAsCustom($project, $repository)) {
            $this->logVerbose(
                '  > Found %s as custom theme.',
                $project
            );
            return $projectInfo;
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
     * @return ProjectInterface|false
     */
    public function getProfileUsageInRepositoryAsCustom($profile, $repository)
    {
        $response = $this->service->raw(
            $repository['name'],
            'custom/profile/' . $profile . '/' . $profile . 'info'
        );

        return array_key_exists('content', $response)
            ? (new Project\ProjectFactory())->create($profile, $repository)
            : false;
    }

    /**
     * Search a module within a repository as a custom module.
     *
     * @param string $module
     *   The module or theme name.
     * @param array $repository
     *   The repository to search in.
     *
     * @return ProjectInterface|false
     */
    public function getModuleUsageInRepositoryAsCustom($module, $repository)
    {
        $response = $this->service->raw(
            $repository['name'],
            "custom/modules/custom/{$module}/{$module}.info"
        );

        return array_key_exists('content', $response)
            ? (new Project\ProjectFactory())->create($module, $repository)
            : false;
    }

    /**
     * Search a module within a repository as a custom feature.
     *
     * @param string $feature
     *   The module or theme name.
     * @param array $repository
     *   The repository to search in.
     *
     * @return ProjectInterface|false
     */
    public function getFeatureUsageInRepositoryAsCustom($feature, $repository)
    {
        $response = $this->service->raw(
            $repository['name'],
            "custom/modules/features/{$feature}/{$feature}.info"
        );

        return array_key_exists('content', $response)
            ? (new Project\ProjectFactory())->create($profile, $repository)
            : false;
    }

    /**
     * Search a theme within a repository as a custom theme.
     *
     * @param string $theme
     *   The module or theme name.
     * @param array $repository
     *   The repository to search in.
     *
     * @return ProjectInterface|false
     */
    public function getThemeUsageInRepositoryAsCustom($theme, $repository)
    {
        $response = $this->service->raw(
            $repository['name'],
            "custom/themes/{$theme}/{$theme}.info"
        );

        return array_key_exists('content', $response)
            ? (new Project\ProjectFactory())->create($profile, $repository)
            : false;
    }
}
