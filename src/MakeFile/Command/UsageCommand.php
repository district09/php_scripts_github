<?php

namespace DigipolisGent\Github\MakeFile\Command;

use DigipolisGent\Github\Core\Command\AbstractCommand;
use DigipolisGent\Github\Core\Filter;
use DigipolisGent\Github\Core\Handler\ProjectsUsageHandler;
use DigipolisGent\Github\Core\Handler\RepositoryHandler;
use DigipolisGent\Github\Core\Service\Source;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Find sites that use the given project(s) (install profile, module, theme).
 *
 * @package DigipolisGent\Github\MakeFile\Command
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
            ->setName('makefile:usage')
            ->setDescription('Find project usages.')
            ->setHelp('Find the MakeFile sites that use one of the given projects (install profile, module or theme).')
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
        $filters = new Filter\FilterSet();
        $filters->addFilter(new Filter\Type('drupal_site'));

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

            if (empty($found['brick']) && empty($found['custom'])) {
                $this->outputStyle->note('Project not used.');
                continue;
            }

            if (!empty($found['brick'])) {
                $this->outputStyle->text('Used as Building Brick:');
                $this->outputStyle->listing($found['brick']);
            }

            if (!empty($found['custom'])) {
                $this->outputStyle->text('Used as Custom Brick:');
                $this->outputStyle->listing($found['custom']);
            }

            $this->outputStyle->newLine();
        }
    }
}
