<?php

namespace DigipolisGent\Github\MakeFile;

use DigipolisGent\Github\Core\DependencyFile\DependencyFileAbstract;
use DigipolisGent\Github\Core\Project\ProjectInterface;
use DigipolisGent\Github\MakeFile\Project\ProjectFactory;
use Symfony\Component\Yaml\Yaml;

/**
 * Wrapper around the makefile contents.
 */
class MakeFile extends DependencyFileAbstract
{
    /**
     * Create a new MakeFile object based on a Yaml raw file content.
     *
     * @param string $raw
     *   Raw Yaml input.
     *
     * @return MakeFile
     */
    public static function fromRaw($raw)
    {
        $parsed = Yaml::parse($raw);
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
        if (!isset($this->data['projects'][$name])) {
            return false;
        }

        $factory = new ProjectFactory();
        return $factory->create($name, $this->data['projects'][$name]);
    }

}
