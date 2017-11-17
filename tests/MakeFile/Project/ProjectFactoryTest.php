<?php

namespace DigipolisGent\Tests\Github\MakeFile\Project;

use DigipolisGent\Github\MakeFile\Project\ProjectFactory;
use PHPUnit\Framework\TestCase;

/**
 * Test the MakeFile project wrapper.
 */
class ProjectFactoryTest extends TestCase
{

    /**
     * Test the create functionality.
     *
     * @dataProvider projectDataProvider
     */
    public function testCreate($expected, $data)
    {
        $factory = new ProjectFactory();
        $project = $factory->create('name', $data);

        if ($expected === false) {
            $this->assertFalse($project);
        } else {
            $this->assertInstanceOf($expected, $project);
        }
    }

    /**
     * Data provider for the project creator.
     *
     * @return array
     *   Array with the 2 values per row:
     *   - Expected result.
     *   - Data to create the object.
     */
    public function projectDataProvider()
    {
        return [
            [
                false,
                ['location' => 'https://foo.bar'],
            ],
            [
                'DigipolisGent\Github\MakeFile\Project\Ddo',
                ['version' => '1.7'],
            ],
            [
                'DigipolisGent\Github\MakeFile\Project\Download',
                ['download' => ['type' => 'get']],
            ],
            [
                'DigipolisGent\Github\MakeFile\Project\Duplo',
                ['location' => 'https://duplo.gentgrp.gent.be/release-history'],
            ],
            [
                'DigipolisGent\Github\MakeFile\Project\Git',
                ['download' => ['type' => 'git']],
            ],
        ];
    }
}
