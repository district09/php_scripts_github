<?php

namespace DigipolisGent\Github\MakeFile\Project;

use DigipolisGent\Github\Core\Project\InvalidSourceException;
use DigipolisGent\Github\Core\Project\ProjectAbstract;

/**
 * project hosted on Drupal.org (ddo).
 */
class Ddo extends ProjectAbstract
{
    /**
     * @inheritDoc
     */
    public static function fromArray($name, array $data)
    {
        if (!empty($data['location'])) {
            throw new InvalidSourceException('Source is custom location.');
        }
        if (!empty($data['download'])) {
            throw new InvalidSourceException('Source is custom download.');
        }

        return parent::fromArray($name, $data);
    }

    /**
     * @inheritDoc
     */
    public function getSourceType()
    {
        return 'ddo';
    }

    /**
     * @inheritDoc
     */
    public function getSourceUrl()
    {
        $url = sprintf('drupal.org/project/%s', $this->getName());
        return $url;
    }
}
