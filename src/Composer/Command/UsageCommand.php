<?php

namespace DigipolisGent\Github\Composer\Command;

use DigipolisGent\Github\Core\Command\AbstractCommand;
use DigipolisGent\Github\Core\Filter;
use DigipolisGent\Github\Core\Handler;
use DigipolisGent\Github\Core\Log\ConsoleLogger;
use DigipolisGent\Github\Core\Service\Source;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Find sites that use the given project(s).
 *
 * @package DigipolisGent\Github\Composer\Command
 */
class UsageCommand extends AbstractCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('composer:usage')
            ->setDescription('Find project usages.')
            ->setHelp('Find the Composer sites that use one of the given projects.')
        ;

        // Github login.
        $this->configureLogin();

        // Optional branch to search in the site repositories.
        $this
            ->addOption(
                'branch',
                'b',
                InputOption::VALUE_OPTIONAL,
                'The branchname to search in.',
                'develop'
            );

        // The team name to search in.
        $this->configureTeamName();

        // One or more projects to search for.
        $this
            ->addArgument(
                'project',
                InputArgument::REQUIRED | InputArgument::IS_ARRAY,
                'One or more project names to search for'
            );
    }

    /**
     * @inheritdoc
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get the output style and create the logger.
        $io = new SymfonyStyle($input, $output);
        $logger = new ConsoleLogger($io);

        // Authenticate the client.
        $this->authenticate($input);

        // Get all drupal8_site and php_package repositories.
        $handler = new Handler\RepositoriesFilteredHandler(
            $this->getGithubClient(),
            [
                new Filter\Type(['drupal8_site']),
                new Filter\Type(['php_package']),
            ]
        );
        $handler->setLogger($logger);
        $repositories = $handler->getRepositories(
            $input->getArgument('team')
        );

        // Search in the repositories.
        $handler = new Handler\ProjectsUsageHandler(
            $this->getSourceService($input),
            $input->getArgument('project')
        );
        $handler->setLogger($logger);
        $usages = $handler->getUsages($repositories);

        $this->writeToScreen($io, $usages);
    }

    /**
     * Write the output to the screen.
     *
     * @param SymfonyStyle $io
     * @param array $usages
     */
    protected function writeToScreen(SymfonyStyle $io, array $usages)
    {
        foreach ($usages as $project => $found) {
            $io->section($project);

            if (empty($found)) {
                $io->note('Project not used.');
                continue;
            }
            if (!empty($found)) {
                $io->text('Used as Package:');
                $io->listing($found);
            }

            $io->newLine();
        }
    }

    /**
     * Get the Github source service.
     *
     * @param InputInterface $input
     *
     * @return Source
     */
    protected function getSourceService(InputInterface $input)
    {
        return new Source(
            $this->getGithubClient(),
            $input->getArgument('team'),
            $input->getOption('branch')
        );
    }
}
