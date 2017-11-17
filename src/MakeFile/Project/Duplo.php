<?php

namespace DigipolisGent\Github\MakeFile\Project;

use DigipolisGent\Github\Core\Project\InvalidSourceException;
use DigipolisGent\Github\Core\Project\ProjectAbstract;

/**
 * project hosted on Drupal.org (ddo).
 */
class Duplo extends ProjectAbstract
{
    /**
     * @inheritDoc
     */
    public static function fromArray($name, array $data)
    {
        if (empty($data['location'])) {
            throw new InvalidSourceException('Location is not Duplo.');
        }
        if (!preg_match('#^https?://duplo\.#', $data['location'])) {
            throw new InvalidSourceException('Location is not Duplo.');
        }

        return parent::fromArray($name, $data);
    }

    /**
     * @inheritDoc
     */
    public function getSourceType()
    {
        return 'duplo';
    }

    /**
     * @inheritDoc
     */
    public function getSourceUrl()
    {
        return sprintf(
            'https://duplo.gentgrp.gent.be/project/%s',
            $this->getName()
        );
    }
}
