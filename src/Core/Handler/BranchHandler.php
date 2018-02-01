<?php

namespace DigipolisGent\Github\Core\Handler;

/**
 * Handler for branch API calls.
 *
 * @package DigipolisGent\Github\Core\Handler
 */
class BranchHandler extends HandlerAbstract
{
    /**
     * Get the details of a branch.
     *
     * @param string $owner
     *   The repository owner.
     * @param string $name
     *   The repository name.
     * @param string $branch
     *   The branch name.
     *
     * @return array
     *   The branch details.
     */
    public function getBranch($owner, $name, $branch)
    {
        $this->logVerbose(
            'Fetching details of %s branch of repository %s/%s...',
            $branch,
            $owner,
            $name
        );

        return $this->client->api('repo')
            ->branches($owner, $name, $branch);
    }

    /**
     * Get the protection configuration for a branch.
     *
     * @param string $owner
     *   The repository owner.
     * @param string $name
     *   The repository name.
     * @param string $branch
     *   The branch name.
     *
     * @return array
     *   The protection configuration.
     *
     * @throws \Exception
     */
    public function getProtection($owner, $name, $branch)
    {
        $this->logVerbose(
            'Fetching protection of %s branch of repository %s/%s...',
            $branch,
            $owner,
            $name
        );

        return $this->client->api('repo')
            ->protection()
            ->show($owner, $name, $branch);
    }

    /**
     * Get the statuses for the tip of a branch.
     *
     * @param string $owner
     *   The repository owner.
     * @param string $name
     *   The repository name.
     * @param string $branch
     *   The branch name.
     *
     * @return array
     *   The statuses for that branch.
     *
     * @throws \Exception
     */
    public function getStatuses($owner, $name, $branch)
    {
        $this->logVerbose(
            'Fetching statuses for %s branch of repository %s/%s...',
            $branch,
            $owner,
            $name
        );

        return $this->client->api('repo')
            ->statuses()
            ->show($owner, $name, $branch);
    }

    /**
     * Configure branch protection.
     *
     * @param string $owner
     *   The repository owner.
     * @param string $name
     *   The repository name.
     * @param string $branch
     *   The branch name.
     * @param array $values
     *   Array of values to post.
     *
     * @throws \Exception
     */
    public function protect($owner, $name, $branch, array $values)
    {
        $this->logVerbose(
            'Updating protection of %s branch of repository %s/%s...',
            $branch,
            $owner,
            $name
        );

        $this->client->api('repo')
            ->protection()
            ->update($owner, $name, $branch, $values);
    }
}
