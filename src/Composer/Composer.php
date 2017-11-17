<?php

namespace DigipolisGent\Github\Composer;

use DigipolisGent\Github\Core\DependencyFile\DependencyFileAbstract;
use DigipolisGent\Github\Core\Project\ProjectInterface;
use DigipolisGent\Github\Composer\Project\ProjectFactory;

/**
 * Wrapper around the composer.lock contents.
 */
class Composer extends DependencyFileAbstract
{
    /**
     * Create a new Composer object based on a JSON raw file content.
     *
     * @param string $raw
     *   Raw JSON input.
     *
     * @return Composer
     */
    public static function fromRaw($raw)
    {
        $parsed = json_decode($raw, true);
        if (!is_array($parsed)) {
            $parsed = [];
        }

        return static::fromArray($parsed);
    }

    /**
     * Search a project in the MakeFile.
     *
     * @param string $name
     *   The project name to search for.
     *
     * @return ProjectInterface|false
     *   The project object (if in data).
     */
    public function searchProject($name)
    {
        if (array_key_exists('packages', $this->data)) {
            foreach ($this->data['packages'] as $package_key => $package_info) {
                if (substr($package_info['name'], -strlen($name)) === $name) {
                    $factory = new ProjectFactory();
                    return $factory->create($name, $this->data['packages'][$package_key]);
                }
            }
        }
        if (array_key_exists('packages-dev', $this->data)) {
            foreach ($this->data['packages-dev'] as $package_key => $package_info) {
                if (substr($package_info['name'], -strlen($name)) === $name) {
                    $factory = new ProjectFactory();
                    return $factory->create($name, $this->data['packages-dev'][$package_key]);
                }
            }
        }

        return false;
    }

}
