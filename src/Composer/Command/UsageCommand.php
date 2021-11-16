<?php

namespace DigipolisGent\Github\Composer\Command;

use DigipolisGent\Github\Composer\ComposerProjectsUsageHandler;
use DigipolisGent\Github\Core\Command\AbstractCommand;
use DigipolisGent\Github\Core\Filter\FilterSet;
use DigipolisGent\Github\Core\Filter\Type;
use DigipolisGent\Github\Core\Handler\RepositoryHandler;
use DigipolisGent\Github\Core\Project\Usage;
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
        $filters->addFilter(new Type('drupal_site'));
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

        $projects = $input->getArgument('project');
        $source = new Source(
            $this->getClient(),
            $this->getOrganisation($input),
            $input->getOption('branch')
        );

        if (count($projects) > 1) {
            // Multiple projects means we'll be querying the same repository
            // multiple times. Better enable cache.
            $source->enableCache();
        }

        // Search in the repositories.
        $handler = new ComposerProjectsUsageHandler(
            $source,
            $projects
        );
        $handler->setLogger($this->logger);
        $usages = $handler->getUsages($repositories);

        usort($usages, function (Usage $usage1, Usage $usage2) {
            $projectCompare = strcmp($usage1->getProject()->getName(), $usage2->getProject()->getName());

            return $projectCompare === 0
              ? strcmp($usage1->getRepository(), $usage2->getRepository())
              : $projectCompare;
        });

        $this->writeToScreen($usages, $projects);

        return 0;
    }

    /**
     * Write the output to the screen.
     *
     * @param Usage[] $usages
     *   List of usages.
     * @param array $projects
     *   List of projects that were queried.
     */
    protected function writeToScreen(array $usages, $projects)
    {
        $projectName = false;
        $table = false;
        foreach ($usages as $usage) {
            if ($projectName !== $usage->getProject()->getName()) {
                $projectName = $usage->getProject()->getName();
                $this->outputStyle->section($usage->getProject()->getName());
                $this->outputStyle->text('Used as Package:');
                if ($table && $table['rows']) {
                    $this->outputStyle->table($table['headers'], $table['rows']);
                }
                $table = [
                    'headers' => ['Repository', 'Version'],
                    'rows' => [],
                ];
                $projects = array_diff($projects, [$usage->getProject()->getName()]);
            }
            $table['rows'][] = [$usage->getRepository(), $usage->getProject()->getVersion()];
        }

        if ($table && $table['rows']) {
            $this->outputStyle->table($table['headers'], $table['rows']);
        }

        foreach ($projects as $project) {
            $this->outputStyle->section($project);
            $this->outputStyle->note('Project not used.');
        }
    }
}
