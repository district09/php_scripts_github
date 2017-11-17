<?php

namespace DigipolisGent\Tests\Github\MakeFile\Project;

use DigipolisGent\Github\MakeFile\Project\Git;
use PHPUnit\Framework\TestCase;

/**
 * Test the MakeFile project wrapper.
 */
class GitTest extends TestCase
{
    /**
     * Minimal data to create a Git project object.
     *
     * @var array
     */
    private $data = ['download' => ['type' => 'git']];

    /**
     * Test the exception if download key in data is empty.
     *
     * @expectedException \DigipolisGent\Github\Core\Project\InvalidSourceException
     * @expectedExceptionMessage Source is not Git.
     */
    public function testExceptionWhenDownloadIsNotSet()
    {
        Git::fromArray('name', []);
    }

    /**
     * Test the exception if download-type is not git.
     *
     * @expectedException \DigipolisGent\Github\Core\Project\InvalidSourceException
     * @expectedExceptionMessage Source is not Git.
     */
    public function testExceptionWhenDownloadTypeIsNotGit()
    {
        $data = [
            'download' => ['type' => 'get'],
        ];
        Git::fromArray('name', $data);
    }

    /**
     * Test getting the version number.
     */
    public function testGetVersion()
    {
        $project = Git::fromArray('name', $this->data);
        $this->assertNull($project->getVersion());

        // Version from the tag info.
        $data = $this->data;
        $tag = '7.x-1.0';
        $data['download']['tag'] = $tag;
        $project = Git::fromArray('name', $data);
        $this->assertEquals($tag . ' (tag)', $project->getVersion());

        // Version from the revision (commit) info.
        $data = $this->data;
        $revision = 'ee8b95c';
        $data['download']['revision'] = $revision;
        $project = Git::fromArray('name', $data);
        $this->assertEquals($revision . ' (revision)', $project->getVersion());

        // Version from the branch.
        $data = $this->data;
        $branch = 'develop';
        $data['download']['branch'] = $branch;
        $project = Git::fromArray('name', $data);
        $this->assertEquals($branch . ' (branch)', $project->getVersion());
    }

    /**
     * Test getting the source type.
     */
    public function testGetSourcetype()
    {
        $project = Git::fromArray('name', $this->data);
        $this->assertEquals('git', $project->getSourceType());
    }

    /**
     * Test getting te source domain name.
     */
    public function testGetSourceUrl()
    {
        // No URL set.
        $project = Git::fromArray('name', $this->data);
        $this->assertNull($project->getSourceUrl());

        // URL set.
        $data = $this->data;
        $url = 'https://github.com/DigipolisGentgent/drupal_module_culturefeed-lists';
        $data['download']['url'] = $url;
        $project = Git::fromArray('name', $data);
        $this->assertEquals($url, $project->getSourceUrl());
    }
}
