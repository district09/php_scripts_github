<?php

namespace DigipolisGent\Github\MakeFile\Command;

use DigipolisGent\Github\Core\Command\AbstractCommand;
use DigipolisGent\Github\Core\Filter;
use DigipolisGent\Github\Core\Handler\RepositoryHandler;
use DigipolisGent\Github\Core\Service\Source;
use DigipolisGent\Github\MakeFile\MakeFileProjectsUsageHandler;
use DigipolisGent\Github\MakeFile\Project\MakeFileUsage;
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

        $projects = $input->getArgument('project');

        // Search in the repositories.
        $handler = new MakeFileProjectsUsageHandler(
            new Source(
                $this->getClient(),
                $this->getOrganisation($input),
                $input->getOption('branch')
            ),
          $projects
        );
        $handler->setLogger($this->logger);
        $usages = $handler->getUsages($repositories);

        usort($usages, function (MakeFileUsage $usage1, MakeFileUsage $usage2) {
            $projectCompare = strcmp($usage1->getProject()->getName(), $usage2->getProject()->getName());
            if ($projectCompare !== 0) {
                return $projectCompare;
            }

            $usageTypeCompare = strcmp($usage1->getType(), $usage2->getType());
            return $usageTypeCompare === 0
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
                if ($table && $table['rows']) {
                    $this->outputStyle->table($table['headers'], $table['rows']);
                }
                $table = [
                    'headers' => ['Repository', 'Type', 'Version'],
                    'rows' => [],
                ];

                $projects = array_diff($projects, [$usage->getProject()->getName()]);
            }

            $table['rows'][] = [
                $usage->getRepository(),
                $usage->getType() === MakeFileUsage::USAGE_BRICK
                    ? 'Building Brick'
                    : 'Custom Brick',
                $usage->getProject()->getVersion(),
            ];
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
