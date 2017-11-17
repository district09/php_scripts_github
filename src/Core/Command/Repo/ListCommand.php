<?php

namespace DigipolisGent\Github\Core\Command\Repo;

use DigipolisGent\Github\Core\Command\AbstractCommand;
use DigipolisGent\Github\Core\Filter;
use DigipolisGent\Github\Core\Handler;
use DigipolisGent\Github\Core\Log\ConsoleLogger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * List available repositories in a team repository.
 *
 * @package DigipolisGent\Github\Command\Repo
 */
class ListCommand extends AbstractCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('repo:list')
            ->setDescription('List repositories.')
            ->setHelp('List repositories belonging to the provided organisation.')
        ;

        // Github login.
        $this->configureLogin();

        // Filter by regular expression.
        $this
            ->addOption(
                'pattern',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Regular expression pattern to filter the repositories by name'
            )
        ;

        // Filter by repo types.
        $typesHelp = sprintf(
            'Repository type to filter by (%s)',
            implode(', ', Filter\Type::allowedTypes())
        );
        $this
            ->addOption(
                'type',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                $typesHelp
            )
        ;

        // The team name is the only argument.
        $this->configureTeamName();
    }

    /**
     * @inheritdoc
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Output style.
        $io = new SymfonyStyle($input, $output);

        // Create the logger.
        $logger = new ConsoleLogger($io);

        // Get the proper handler.
        $this->getGithubClient();
        $this->authenticate($input);
        $filters = $this->getFilters($input);
        $handler = count($filters)
          ? new Handler\RepositoriesFilteredHandler($this->client, $filters)
          : new Handler\RepositoriesHandler($this->client);
        $handler->setLogger($logger);

        // Get the repositories by the team name.
        $repositories = $handler->getRepositories(
            $input->getArgument('team')
        );

        $this->writeToScreen($io, $repositories);
    }

    /**
     * Write the output to the screen.
     *
     * @param SymfonyStyle $io
     * @param array $repositories
     */
    protected function writeToScreen(SymfonyStyle $io, array $repositories)
    {
        // Create table content.
        $items = [];
        foreach ($repositories as $repository) {
            $items[] = array(
                $repository['name'],
                $repository['full_name'],
            );
        }

        $io->table(
            ['Name', 'Full Name'],
            $items
        );
    }

    /**
     * Get the filters from the input.
     *
     * @param InputInterface $input
     *
     * @return Filter\FilterInterface[]
     */
    protected function getFilters($input)
    {
        $filters = [];

        $patterns = $input->getOption('pattern');
        if (!empty($patterns)) {
            $filters[] = new Filter\Pattern($patterns);
        }

        $types = $input->getOption('type');
        if (!empty($types)) {
            $filters[] = new Filter\Type($types);
        }

        return $filters;
    }
}
