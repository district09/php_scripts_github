<?php

namespace DigipolisGent\Tests\Github\MakeFile;

use DigipolisGent\Github\MakeFile\MakeFile;
use PHPUnit\Framework\TestCase;

/**
 * Test the MakeFile project wrapper.
 */
class MakeFileTest extends TestCase
{
    /**
     * Create from array.
     */
    public function testFromAray()
    {
        $data = [
            'projects' => [
                'drupal' => ['version' => '7.53'],
            ],
        ];
        $makeFile = MakeFile::fromArray($data);

        $this->assertEquals(
            $data,
            $makeFile->toArray()
        );
    }

    /**
     * Create from yaml structure.
     */
    public function testFromRaw()
    {
        $raw = <<<EOT
projects:
  drupal:
    version: "7.53"
EOT;
        $makeFile = MakeFile::fromRaw($raw);

        $this->assertEquals(
            '7.53',
            $makeFile->searchProject('drupal')->getVersion()
        );
    }

    /**
     * Create from invalid raw strings.
     */
    public function testFromInvalidRaw()
    {
        // XML.
        $raw = '<test>123</test>';
        $makeFile = MakeFile::fromRaw($raw);
        $this->assertEquals([], $makeFile->toArray());
    }


    /**
     * Search project.
     */
    public function testSearchProject()
    {
        $data = [
            'projects' => [
                'drupal' => ['version' => '7.53'],
            ],
        ];
        $makeFile = MakeFile::fromArray($data);

        // Search not existing project.
        $this->assertFalse($makeFile->searchProject('foo_bar'));

        // Search existing project.
        $project = $makeFile->searchProject('drupal');
        $this->assertEquals('drupal', $project->getName());
    }

}
