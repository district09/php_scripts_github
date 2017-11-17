<?php

namespace DigipolisGent\Github\Composer\Project;

use DigipolisGent\Github\Core\Project\InvalidSourceException;
use DigipolisGent\Github\Core\Project\ProjectAbstract;

/**
 * project hosted on Drupal.org (ddo).
 */
class Package extends ProjectAbstract
{
    /**
     * @inheritDoc
     */
    public static function fromArray($name, array $data)
    {
        return parent::fromArray($name, $data);
    }

    /**
     * @inheritDoc
     */
    public function getVersion()
    {
        return $this->getDataValue('version', array());
    }

    /**
     * @inheritDoc
     */
    public function getSourceType()
    {
        return 'package';
    }

    /**
     * @inheritDoc
     */
    public function getSourceUrl()
    {
        $dist = $this->getDataValue('dist', array());
        if (empty($dist['url'])) {
            return null;
        }
        return $dist['url'];
    }
}
