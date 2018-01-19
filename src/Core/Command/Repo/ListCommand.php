<?php

namespace DigipolisGent\Github\Core\Command\Repo;

use DigipolisGent\Github\Core\Handler\RepositoryHandler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * List available repositories in an organisation.
 *
 * @package DigipolisGent\Github\Core\Command\Repo
 */
class ListCommand extends AbstractCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('repo:list')
            ->setDescription('List repositories.')
            ->setHelp('List the repositories that belong to specified organisation.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $items = [];
        foreach ($this->getRepositories($input) as $repository) {
            $items[] = array(
                $repository['name'],
                $repository['full_name'],
            );
        }

        $this->outputStyle->table(
            ['Name', 'Full Name'],
            $items
        );
    }
}
