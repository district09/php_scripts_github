<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace DigipolisGent\Github\MakeFile\Project;

use DigipolisGent\Github\Core\Project\ProjectInterface;
use DigipolisGent\Github\Core\Project\Usage;

/**
 * Description of MakeFileUsage
 *
 * @author jelle
 */
class MakeFileUsage extends Usage {
    public const USAGE_BRICK = 'brick';
    public const USAGE_CUSTOM = 'custom';

    protected $type;

    public function __construct(ProjectInterface $project, $repository, $type) {
        parent::__construct($project, $repository);
        $this->type = $type;
    }

    function getType() {
        return $this->type;
    }

}
