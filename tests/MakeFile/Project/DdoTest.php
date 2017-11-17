<?php

namespace DigipolisGent\Tests\Github\MakeFile\Project;

use DigipolisGent\Github\MakeFile\Project\Ddo;
use PHPUnit\Framework\TestCase;

/**
 * Test the MakeFile project wrapper.
 */
class DdoTest extends TestCase
{
    /**
     * Test the exception if a location is in the data.
     *
     * @expectedException \DigipolisGent\Github\Core\Project\InvalidSourceException
     * @expectedExceptionMessage Source is custom location.
     */
    public function testExceptionWhenLocationIsSet()
    {
        $data = ['location' => 'https://location.is/set'];
        DDo::fromArray('name', $data);
    }

    /**
     * Test the exception if download is in the data.
     *
     * @expectedException \DigipolisGent\Github\Core\Project\InvalidSourceException
     * @expectedExceptionMessage Source is custom download.
     */
    public function testExceptionWhenDownloadIsSet()
    {
        $data = ['download' => ['url' => 'https://location.is/set']];
        DDo::fromArray('name', $data);
    }

    /**
     * Test getting the name.
     */
    public function testGetName()
    {
        $name = 'project_name';
        $project = Ddo::fromArray($name, []);
        $this->assertEquals($name, $project->getName());
    }

    /**
     * Test getting the version number.
     */
    public function testGetVersion()
    {
        $project = Ddo::fromArray('name', []);
        $this->assertNull($project->getVersion());

        $data = ['version' => '1.12'];
        $project = Ddo::fromArray('name', $data);
        $this->assertEquals('1.12', $project->getVersion());
    }

    /**
     * Test getting the type.
     */
    public function testGetType()
    {
        $project = Ddo::fromArray('name', array());
        $this->assertNull($project->getType());

        $data = ['type' => 'module'];
        $project = Ddo::fromArray('name', $data);
        $this->assertEquals('module', $project->getType());
    }

    /**
     * Test getting the source type.
     */
    public function testGetSourcetype()
    {
        $project = Ddo::fromArray('name', []);
        $this->assertEquals('ddo', $project->getSourceType());
    }

    /**
     * test getting te source domain name.
     */
    public function testGetSourceUrl()
    {
        $name = 'project_name';
        $project = Ddo::fromArray($name, []);
        $this->assertEquals(
            'drupal.org/project/' . $name,
            $project->getSourceUrl()
        );
    }
}
