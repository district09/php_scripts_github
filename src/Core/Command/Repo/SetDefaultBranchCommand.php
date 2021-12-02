<?php

namespace DigipolisGent\Github\Core\Command\Repo;

use DigipolisGent\Github\Core\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Set the default branch of a repository.
 *
 * @package DigipolisGent\Github\Core\Command\Repo
 */
class SetDefaultBranchCommand extends AbstractCommand
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
            ->setHelp('Sets the default branch for the repositories of an organisation.')
            ->addRepositoryOptions()
            ->addOrganisationArgument();

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

        // Update the repositories.
        $updated = 0;
        foreach ($this->getRepositories($input) as $repository) {
            try {
                if ($this->updateRepository($repository, $branch)) {
                    $updated++;
                }
            } catch (\Exception $ex) {
                $this->logger->error($ex->getMessage());
            }
        }

        $this->logger->notice(sprintf('Updated %d repositories.', $updated));

        return 0;
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
            $this->getRepositoryHandler()->update(
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
