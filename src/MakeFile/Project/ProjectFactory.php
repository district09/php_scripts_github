<?php

namespace DigipolisGent\Github\MakeFile\Project;

use DigipolisGent\Github\Core\Project\InvalidSourceException;
use DigipolisGent\Github\Core\Project\ProjectInterface;

/**
 * Factory to create the proper ProjectInterface object based on the data.
 */
class ProjectFactory
{
    /**
     * Available project types to create project objects for.
     *
     * @var array
     */
    private $types = [
        'DigipolisGent\Github\MakeFile\Project\Ddo',
        'DigipolisGent\Github\MakeFile\Project\Download',
        'DigipolisGent\Github\MakeFile\Project\Duplo',
        'DigipolisGent\Github\MakeFile\Project\Git',
    ];

    /**
     * Method to create the proper object based on the data.
     *
     * @param string $name
     *   The project name.
     * @param array $data
     *   The project data.
     *
     * @return ProjectInterface|false
     *   Retuns false is no project object could be created.
     */
    public function create($name, array $data)
    {
        foreach ($this->types as $type) {
            try {
                return $type::fromArray($name, $data);
            } catch (InvalidSourceException $e) {
                // Do nothing if exception was thrown.
            }
        }

        return false;
    }
}
