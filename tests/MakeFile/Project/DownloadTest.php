<?php

namespace DigipolisGent\Tests\Github\MakeFile\Project;

use DigipolisGent\Github\MakeFile\Project\Download;
use PHPUnit\Framework\TestCase;

/**
 * Test the MakeFile project wrapper.
 */
class DownloadTest extends TestCase
{
    /**
     * Minimal data to create a Git project object.
     *
     * @var array
     */
    private $data = ['download' => ['type' => 'get']];

    /**
     * Test the exception if download key in data is empty.
     *
     * @expectedException \DigipolisGent\Github\Core\Project\InvalidSourceException
     * @expectedExceptionMessage Source is not download.
     */
    public function testExceptionWhenDownloadIsNotSet()
    {
        Download::fromArray('name', []);
    }

    /**
     * Test the exception if download-type is not "file" or "get".
     *
     * @expectedException \DigipolisGent\Github\Core\Project\InvalidSourceException
     * @expectedExceptionMessage Source is not download.
     */
    public function testExceptionWhenDownloadTypeIsNotFileOrGet()
    {
        $data = [
            'download' => ['type' => 'foo'],
        ];
        Download::fromArray('name', $data);
    }

    /**
     * Test getting the source type.
     */
    public function testGetSourcetype()
    {
        $project = Download::fromArray('name', $this->data);
        $this->assertEquals('download:get', $project->getSourceType());

        $data = $this->data;
        $data['download']['type'] = 'file';
        $project = Download::fromArray('name', $data);
        $this->assertEquals('download:file', $project->getSourceType());
    }

    /**
     * Test getting te source domain name.
     */
    public function testGetSourceUrl()
    {
        // No URL set.
        $project = Download::fromArray('name', $this->data);
        $this->assertNull($project->getSourceUrl());

        // URL set.
        $data = $this->data;
        $url = 'https://github.com/DigipolisGentgent/drupal_module_culturefeed-lists/archive/7.x-1.0-beta1.zip';
        $data['download']['url'] = $url;
        $project = Download::fromArray('name', $data);
        $this->assertEquals($url, $project->getSourceUrl());
    }
}
