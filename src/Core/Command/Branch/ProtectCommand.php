<?php

namespace DigipolisGent\Github\Core\Command\Branch;

use DigipolisGent\Github\Core\Command\AbstractCommand;
use DigipolisGent\Github\Core\Handler\BranchHandler;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Protect a branch.
 *
 * @package DigipolisGent\Github\Core\Command\Branch
 */
class ProtectCommand extends AbstractCommand
{
    /**
     * The branch handler.
     *
     * @var BranchHandler
     */
    private $branchHandler;

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('branch:protect')
            ->setDescription('Enable branch protection.')
            ->setHelp('Enable branch protection for a specific branch of the repositories of an organisation.')
            ->addRepositoryOptions()
            ->addOrganisationArgument();

        $this->addOption(
            'require-pr',
            'p',
            InputOption::VALUE_OPTIONAL,
            'All changes must be submitted via a pull request with at least one approved review.',
            '1'
        );

        $this->addOption(
            'dismiss-approvals-on-push',
            'd',
            InputOption::VALUE_OPTIONAL,
            'Dismiss pull request review approvals on push.',
            '1'
        );

        $this->addOption(
            'require-owner-review',
            'o',
            InputOption::VALUE_OPTIONAL,
            'Require a review by a designated code owner.'
        );

        $this->addOption(
            'require-status-check',
            's',
            InputOption::VALUE_OPTIONAL,
            'Ensure the status checks pass before merging a branch.',
            '1'
        );

        $this->addOption(
            'require-up-to-date',
            'l',
            InputOption::VALUE_OPTIONAL,
            'Require the merged branch to be up to date with the latest changes.',
            '1'
        );

        $this->addOption(
            'status-checks',
            'c',
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Status checks to pass.'
        );

        $this->addOption(
            'apply-for-admins',
            'a',
            InputOption::VALUE_OPTIONAL,
            'Apply the requirements for admins as well.',
            '1'
        );

        $this->addArgument(
            'branch',
            InputArgument::REQUIRED,
            'The branch to protect.'
        );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $branch = $input->getArgument('branch');
        $config = $this->getProtectionConfig($input);

        // Update the branches.
        $updated = 0;
        foreach ($this->getRepositories($input) as $repository) {
            try {
                $prepared = $this->prepareConfig($config, $repository, $branch);
                $this->protectBranch($repository, $branch, $prepared);
                $updated++;
            } catch (\Exception $ex) {
                $this->logger->error($ex->getMessage());
            }
        }

        $this->logger->notice(sprintf('Protected %d branches.', $updated));
    }

    /**
     * Get the protection configuration.
     *
     * @param InputInterface $input
     *   The input object.
     *
     * @return array
     *   Config array.
     */
    protected function getProtectionConfig(InputInterface $input)
    {
        $input = $this->convertInput($input);

        $config = [
            'required_pull_request_reviews' => null,
            'required_status_checks' => null,
            'restrictions' => null,
            'enforce_admins' => $input->getBoolOption('apply-for-admins'),
        ];

        if ($input->getBoolOption('require-pr')) {
            $config['required_pull_request_reviews'] = [
                'dismiss_stale_reviews' => $input->getBoolOption('dismiss-approvals-on-push'),
                'require_code_owner_reviews' => $input->getBoolOption('require-owner-review'),
                'dismissal_restrictions' => null,
            ];
        }

        if ($input->getBoolOption('require-status-check')) {
            $contexts = null;
            if ($input->isOptionSpecified('status-checks')) {
                $contexts = $input->getOption('status-checks');

                if (count($contexts) === 1 && $contexts[0] === '') {
                    unset($contexts[0]);
                }
            }

            $config['required_status_checks'] = [
                'strict' => $input->getBoolOption('require-up-to-date'),
                'contexts' => $contexts,
            ];
        }

        return $config;
    }

    /**
     * Get the branch handler.
     *
     * @return BranchHandler
     */
    protected function getBranchHandler()
    {
        if (null === $this->branchHandler) {
            $this->branchHandler = new BranchHandler($this->getClient());

            if ($this->logger) {
                $this->branchHandler->setLogger($this->logger);
            }
        }

        return $this->branchHandler;
    }

    /**
     * Prepare the configuration by merging in some of the current branch settings.
     *
     * @param array $config
     *   The config array.
     * @param array $repository
     *   The repository array.
     * @param $branch
     *   The branch name.
     *
     * @return array
     *   Prepared config array.
     *
     * @throws \Exception
     */
    protected function prepareConfig(array $config, array $repository, $branch)
    {
        $protection = $this->getBranchHandler()->getProtection(
            $repository['owner']['login'],
            $repository['name'],
            $branch
        );

        // Apply the current pull request dismissal restrictions.
        if (isset($config['required_pull_request_reviews'])) {
            $pr = &$config['required_pull_request_reviews'];

            if (!isset($pr['dismissal_restrictions'])) {
                if (!isset($protection['required_pull_request_reviews']['dismissal_restrictions'])) {
                    $restrictions = new \stdClass();
                } else {
                    $restrictions = $this->parseRetrictions(
                        $protection['required_pull_request_reviews']['dismissal_restrictions']
                    );
                }

                $pr['dismissal_restrictions'] = $restrictions;
            }
        }

        // Apply the current restrictions.
        if (!isset($config['restrictions']) && isset($protection['restrictions'])) {
            $config['restrictions'] = $this->parseRetrictions($protection['restrictions']);
        }

        // Apply the default code checks (if we can find them in the branch).
        if (isset($config['required_status_checks']) && !isset($config['required_status_checks']['contexts'])) {
            if ($contexts = $this->getBranchContexts($repository, $branch)) {
                $contexts = array_intersect($contexts, [
                    'codeclimate',
                    'continious-integration/travis-ci',
                ]);
            }

            $config['required_status_checks']['contexts'] = $contexts;
        }

        return $config;
    }

    /**
     * Get the last used contexts in a branch.
     *
     * @param array $repository
     *   The repository array.
     * @param string $branch
     *   The branch array.
     *
     * @return array
     *   List of known contexts.
     *
     * @throws \Exception
     */
    protected function getBranchContexts(array $repository, $branch)
    {
        $branch = $this->getBranchHandler()->getBranch(
            $repository['owner']['login'],
            $repository['name'],
            $branch
        );

        $commit = $branch['commit'];

        if (!isset($commit['parents']) || count($commit['parents']) < 2) {
            // Use the commit itself.
            $statuses = $this->getBranchHandler()->getStatuses(
                $repository['owner']['login'],
                $repository['name'],
                $commit['sha']
            );
        } else {
            // Merge commit, use a parents.
            $parents = array_reverse($commit['parents']);

            foreach ($parents as $parent) {
                $statuses = $this->getBranchHandler()->getStatuses(
                    $repository['owner']['login'],
                    $repository['name'],
                    $parent['sha']
                );

                if ($statuses) {
                    break;
                }
            }
        }

        $contexts = [];

        if ($statuses) {
            // Add the context of each status.
            foreach ($statuses as $status) {
                $contexts[$status['context']] = $status['context'];
            }

            $contexts = array_values($contexts);
        }

        return $contexts;
    }

    /**
     * Parse the received restrictions into an arary with user logins and team slugs.
     *
     * @param array $restrictions
     *   The received restriction array.
     *
     * @return array
     *   The parsed restrictions.
     */
    protected function parseRetrictions(array $restrictions)
    {
        $parsed = [
            'users' => [],
            'teams' => [],
        ];

        if (!empty($restrictions['users'])) {
            foreach ($restrictions['users'] as $user) {
                $parsed['users'][] = $user['login'];
            }
        }

        if (!empty($restrictions['teams'])) {
            foreach ($restrictions['teams'] as $team) {
                $parsed['teams'][] = $team['slug'];
            }
        }

        return $parsed;
    }

    /**
     * Configure branch protection
     *
     * @param array $repository
     *   The repository array.
     * @param string $branch
     *   The branch name.
     * @param array $config
     *   Array with the protection configuration.
     *
     * @throws \Exception
     */
    protected function protectBranch(array $repository, $branch, array $config)
    {
        $this->getBranchHandler()->protect(
            $repository['owner']['login'],
            $repository['name'],
            $branch,
            $config
        );
    }
}
