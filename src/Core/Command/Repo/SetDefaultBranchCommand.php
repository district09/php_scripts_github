<?php

namespace DigipolisGent\Github\Core\Command\Repo;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Set the default branch of a repository.
 *
 * @package DigipolisGent\Github\Core\Command\Repo
 */
class SetDefaultBranchCommand extends AbstractRepoCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('repo:set-default-branch')
            ->setDescription('Set the default branch.')
            ->setHelp('Sets the default branch for the repositories of an organisation.');

        $this->addArgument(
            'branch',
            InputArgument::REQUIRED,
            'The new default branch.'
        );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $branch = $input->getArgument('branch');

        // Update the respoitories.
        $updated = 0;
        foreach ($this->getRepositories($input) as $repository) {
            if ($this->updateRepository($repository, $branch)) {
                $updated++;
            }
        }

        $this->logger->notice(sprintf('Updated %d repositories.', $updated));
    }

    /**
     * Update a repository.
     *
     * @param array $repository
     *   The repository array.
     * @param string $branch
     *   The enw default branch.
     *
     * @return bool
     *   True if the repositroy was updated.
     *
     * @throws \Exception
     */
    protected function updateRepository(array $repository, $branch)
    {
        if ($repository['default_branch'] !== $branch) {
            $this->getHandler()->update(
                $repository['owner']['login'],
                $repository['name'],
                [ 'default_branch' => $branch ]
            );

            return true;
        }

        $this->logger->debug(
            sprintf(
                'Skipped repository %s because the default branch is already %s.',
                $repository['full_name'],
                $branch
            )
        );

        return false;
    }
}
