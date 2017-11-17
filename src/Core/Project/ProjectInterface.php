<?php

namespace DigipolisGent\Github\Core\Project;

/**
 * Interface for projects.
 */
interface ProjectInterface
{
    /**
     * Create an object from a data array.
     *
     * @param string $name
     *   The project name.
     * @param array $data
     *   The project data.
     *
     * @return ProjectInterface
     *
     * @throws InvalidSourceException
     *   When the given data contains a source not valid for the project type.
     */
    public static function fromArray($name, array $data);

    /**
     * Get the name of the project.
     *
     * @return string
     */
    public function getName();

    /**
     * Get the project version number (if any).
     *
     * @return string|null
     */
    public function getVersion();

    /**
     * Get the project type (module, theme, profile, ...)
     *
     * @return string|null
     */
    public function getType();

    /**
     * Get the project source type (ddo, git, get, ...).
     *
     * @return string|null
     */
    public function getSourceType();

    /**
     * Get the source URL.
     *
     * @return string|null
     */
    public function getSourceUrl();
}
