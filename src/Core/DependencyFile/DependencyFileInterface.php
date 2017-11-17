<?php

namespace DigipolisGent\Github\Core\DependencyFile;

use DigipolisGent\Github\Core\Project\ProjectInterface;

/**
 * Interface for a wrapper around dependency files.
 */
interface DependencyFileInterface
{

    /**
     * Create a new DependencyFile object based on raw file contents.
     *
     * @param string $raw
     *   Raw input (Yaml, json, ...).
     *
     * @return DependencyFileInterface
     */
    public static function fromRaw($raw);

    /**
     * Create a new DependencyFile object from a parsed file (array).
     *
     * @param array $data
     *   The parsed content.
     *
     * @return DependencyFileInterface
     */
    public static function fromArray(array $data);

    /**
     * Get the DependencyFile as array.
     *
     * @return array
     */
    public function toArray();

    /**
     * Search a project in the DependencyFile.
     *
     * @param string $name
     *   The project name to search for.
     *
     * @return ProjectInterface|false
     *   The project object (if in data).
     */
    public function searchProject($name);
}
