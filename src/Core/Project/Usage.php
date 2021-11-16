<?php

namespace DigipolisGent\Github\Core\Project;

class Usage
{
    /**
     * @var ProjectInterface
     */
    protected $project;

    /**
     * @var string
     */
    protected $repository;

    public function __construct(ProjectInterface $project, $repository)
    {
        $this->project = $project;
        $this->repository = $repository;
    }

    function getProject(): ProjectInterface
    {
        return $this->project;
    }

    function getRepository(): string
    {
        return $this->repository;
    }


}
