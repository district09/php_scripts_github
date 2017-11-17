<?php

namespace DigipolisGent\Github\Core\DependencyFile;

/**
 * Abstract implementation of the DependencyFile.
 */
abstract class DependencyFileAbstract implements DependencyFileInterface
{
    /**
     * The parsed DependencyFile content.
     *
     * @var array
     */
    protected $data;

    /**
     * Force creating a new project object using named constructors.
     */
    private function __construct()
    {
    }

    /**
     * @inheritDoc
     */
    public static function fromArray(array $data)
    {
        $makeFile = new static();
        $makeFile->data = $data;

        return $makeFile;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return $this->data;
    }
}
