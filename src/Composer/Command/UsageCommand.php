<?php

namespace DigipolisGent\Github\Composer\Command;

use DigipolisGent\Github\Core\Command\AbstractCommand;
use DigipolisGent\Github\Core\Filter\FilterSet;
use DigipolisGent\Github\Core\Filter\Type;
use DigipolisGent\Github\Core\Handler\ProjectsUsageHandler;
use DigipolisGent\Github\Core\Handler\RepositoryHandler;
use DigipolisGent\Github\Core\Service\Source;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
        parent::configure();

        $this
            ->setName('composer:usage')
            ->setDescription('Find project usages.')
            ->setHelp('Find the Composer sites that use one of the given projects.')
            ->addOrganisationArgument();

        // Optional branch to search in the site repositories.
        $this->addOption(
            'branch',
            'b',
            InputOption::VALUE_OPTIONAL,
            'The branchname to search in.',
            'develop'
        );

        // One or more projects to search for.
        $this->addArgument(
            'project',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'One or more project names to search for'
        );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Build the filters.
        $filters = new FilterSet(FilterSet::OPERATOR_OR);
        $filters->addFilter(new Type('drupal8_site'));
        $filters->addFilter(new Type('php_package'));
        $filters->addFilter(new Type('generic_site'));
        $filters->addFilter(new Type('symfony_site'));
        $filters->addFilter(new Type('laravel_site'));

        // Get the handler.
        $handler = new RepositoryHandler($this->getClient());
        $handler->setLogger($this->logger);

        // Get the repositories.
        $repositories = $handler->getByOrganisation(
            $this->getOrganisation($input),
            $filters
        );

        // Search in the repositories.
        $handler = new ProjectsUsageHandler(
            new Source(
                $this->getClient(),
                $this->getOrganisation($input),
                $input->getOption('branch')
            ),
            $input->getArgument('project')
        );
        $handler->setLogger($this->logger);
        $usages = $handler->getUsages($repositories);

        $this->writeToScreen($usages);
    }

    /**
     * Write the output to the screen.
     *
     * @param array $usages
     *   List of usages.
     */
    protected function writeToScreen(array $usages)
    {
        foreach ($usages as $project => $found) {
            $this->outputStyle->section($project);

            if (empty($found)) {
                $this->outputStyle->note('Project not used.');
                continue;
            }

            $this->outputStyle->text('Used as Package:');
            $this->outputStyle->listing($found);
            $this->outputStyle->newLine();
        }
    }
}
