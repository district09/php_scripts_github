<?php

namespace DigipolisGent\Github\Core\Command\Repo;

use DigipolisGent\Github\Core\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Set the features of a repository.
 *
 * @package DigipolisGent\Github\Core\Command\Repo
 */
class SetFeaturesCommand extends AbstractCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('repo:set-features')
            ->setDescription('Control repository features.')
            ->setHelp('Enable or disable specific features for the repositories of an organisation.')
            ->addRepositoryOptions()
            ->addOrganisationArgument();

        // Control the "issues" feature.
        $this->addOption(
            'has-issues',
            'i',
            InputOption::VALUE_OPTIONAL,
            'Enable or disable issues.'
        );

        // Control the "wiki" feature.
        $this->addOption(
            'has-wiki',
            'w',
            InputOption::VALUE_OPTIONAL,
            'Enable or disable projects.'
        );

        // Control the "projects" feature.
        $this->addOption(
            'has-projects',
            'p',
            InputOption::VALUE_OPTIONAL,
            'Enable or disable projects.'
        );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get the features to enable or disable.
        if (!$features = $this->getFeatures($input)) {
            $this->logger->error('Specify at least one feature to enable or disable.');
            return;
        }

        // Update the repositories.
        $updated = 0;
        foreach ($this->getRepositories($input) as $repository) {
            if ($this->updateRepository($repository, $features)) {
                $updated++;
            }
        }

        $this->logger->notice(sprintf('Updated %d repositories.', $updated));
    }

    /**
     * Get the features to enable or disable.
     *
     * @param InputInterface $input
     *   The input object.
     *
     * @return array
     *   Array of specified features.
     */
    protected function getFeatures(InputInterface $input)
    {
        $input = $this->convertInput($input);
        $options = [
            'has-issues' => 'has_issues',
            'has-wiki' => 'has_wiki',
            'has-projects' => 'has_projects',
        ];

        $features = [];
        foreach ($options as $option => $feature) {
            if ($input->isOptionSpecified($option)) {
                $features[$feature] = $input->getBoolOption($option);
            }
        }

        return $features;
    }

    /**
     * Update a repository.
     *
     * @param array $repository
     *   The repository array.
     * @param array $features
     *   Array of features to update.
     *
     * @return bool
     *   True if the repositroy was updated.
     *
     * @throws \Exception
     */
    protected function updateRepository(array $repository, array $features)
    {
        foreach ($features as $feature => $status) {
            if ($repository[$feature] !== $status) {
                $this->getRepositoryHandler()->update(
                    $repository['owner']['login'],
                    $repository['name'],
                    $features
                );

                return true;
            }
        }

        $this->logger->debug(
            sprintf(
                'Skipped repository %s because the features are in the correct state.',
                $repository['full_name']
            )
        );

        return false;
    }
}
