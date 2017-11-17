<?php

namespace DigipolisGent\Tests\Github\MakeFile\Project;

use DigipolisGent\Github\MakeFile\Project\Duplo;
use PHPUnit\Framework\TestCase;

/**
 * Test the MakeFile project wrapper.
 */
class DuploTest extends TestCase
{
    /**
     * Minimal data to create a Duplo project object.
     *
     * @var array
     */
    private $data = ['location' => 'https://duplo.gentgrp.gent.be/release-history'];

    /**
     * Test the exception if a location is in the data.
     *
     * @expectedException \DigipolisGent\Github\Core\Project\InvalidSourceException
     * @expectedExceptionMessage Location is not Duplo.
     */
    public function testExceptionWhenLocationIsNotSet()
    {
        Duplo::fromArray('name', []);
    }

    /**
     * Test the exception if location value is not duplo.
     *
     * @expectedException \DigipolisGent\Github\Core\Project\InvalidSourceException
     * @expectedExceptionMessage Location is not Duplo.
     */
    public function testExceptionWhenLocationIsNotDuplo()
    {
        $data = ['location' => 'https://location.is/not/duplo'];
        Duplo::fromArray('name', $data);
    }

    /**
     * Test getting the source type.
     */
    public function testGetSourcetype()
    {
        $project = Duplo::fromArray('name', $this->data);
        $this->assertEquals('duplo', $project->getSourceType());
    }

    /**
     * test getting te source domain name.
     */
    public function testGetSourceUrl()
    {
        $name = 'project_name';
        $project = Duplo::fromArray($name, $this->data);
        $this->assertEquals(
            'https://duplo.gentgrp.gent.be/project/' . $name,
            $project->getSourceUrl()
        );
    }
}
