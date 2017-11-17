<?php

namespace DigipolisGent\Github\MakeFile\Project;

use DigipolisGent\Github\Core\Project\InvalidSourceException;
use DigipolisGent\Github\Core\Project\ProjectAbstract;

/**
 * project hosted on Drupal.org (ddo).
 */
class Git extends ProjectAbstract
{
    /**
     * @inheritDoc
     */
    public static function fromArray($name, array $data)
    {
        if (empty($data['download']['type'])) {
            throw new InvalidSourceException('Source is not Git.');
        }
        if ($data['download']['type'] !== 'git') {
            throw new InvalidSourceException('Source is not Git.');
        }

        return parent::fromArray($name, $data);
    }

    /**
     * @inheritDoc
     */
    public function getVersion()
    {
        $info = $this->getDataValue('download', array());

        if (array_key_exists('tag', $info)) {
            return sprintf('%s (tag)', $info['tag']);
        }
        if (array_key_exists('revision', $info)) {
            return sprintf('%s (revision)', $info['revision']);
        }
        if (array_key_exists('branch', $info)) {
            return sprintf('%s (branch)', $info['branch']);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getSourceType()
    {
        return 'git';
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
