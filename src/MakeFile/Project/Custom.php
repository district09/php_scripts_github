<?php

namespace DigipolisGent\Github\MakeFile\Project;

use DigipolisGent\Github\Core\Project\InvalidSourceException;
use DigipolisGent\Github\Core\Project\ProjectAbstract;

/**
 * Custom project.
 */
class Custom extends ProjectAbstract
{

    /**
     * @inheritDoc
     */
    public function getSourceType()
    {
        return 'custom';
    }

    /**
     * @inheritDoc
     */
    public function getSourceUrl()
    {
        return null;
    }

    public function getVersion() {
        return 'N/A';
    }
}
