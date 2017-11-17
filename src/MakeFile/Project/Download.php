<?php

namespace DigipolisGent\Github\MakeFile\Project;

use DigipolisGent\Github\Core\Project\InvalidSourceException;
use DigipolisGent\Github\Core\Project\ProjectAbstract;

/**
 * project hosted on Drupal.org (ddo).
 */
class Download extends ProjectAbstract
{
    /**
     * @inheritDoc
     */
    public static function fromArray($name, array $data)
    {
        if (empty($data['download']['type'])) {
            throw new InvalidSourceException('Source is not download.');
        }
        $types = ['file', 'get'];
        if (!in_array($data['download']['type'], $types)) {
            throw new InvalidSourceException('Source is not download.');
        }

        return parent::fromArray($name, $data);
    }

    /**
     * @inheritDoc
     */
    public function getSourceType()
    {
        $info = $this->getDataValue('download');
        return sprintf('download:%s', $info['type']);
    }

    /**
     * @inheritDoc
     */
    public function getSourceUrl()
    {
        $info = $this->getDataValue('download', array());
        if (empty($info['url'])) {
            return null;
        }
        return $info['url'];
    }
}
