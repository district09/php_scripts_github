<?php

namespace DigipolisGent\Github\Core\Project;

/**
 * Abstract implementation of the ProjectInterface.
 */
abstract class ProjectAbstract implements ProjectInterface
{
    /**
     * The project name.
     *
     * @var string
     */
    private $name;

    /**
     * The data array.
     *
     * @var array
     */
    private $data = [];

    /**
     * Force creating a new project object using named constructors.
     */
    private function __construct()
    {
    }

    /**
     * @inheritdoc
     */
    public static function fromArray($name, array $data)
    {
        $project = new static();
        $project->name = $name;
        $project->data = $data;
        return $project;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getVersion()
    {
        return $this->getDataValue('version', null);
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->getDataValue('type');
    }

    /**
     * Helper to extract a value from the data array.
     *
     * @param string $key
     *   The data array key.
     * @param mixed $default
     *   The default value if the key is not within the data array.
     *
     * @return mixed
     */
    protected function getDataValue($key, $default = null)
    {
        if (!array_key_exists($key, $this->data)) {
            return null;
        }

        return $this->data[$key];
    }
}
