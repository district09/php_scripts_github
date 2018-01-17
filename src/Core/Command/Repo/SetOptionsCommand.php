<?php

namespace DigipolisGent\Github\Core\Command\Repo;

use DigipolisGent\Github\Core\Handler\RepositoriesHandler;
use DigipolisGent\Github\Core\Log\ConsoleLogger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Set some options for the repositories of a team.
 *
 * @package DigipolisGent\Github\Command\Repo
 */
class SetOptionsCommand extends ListCommand
{
    /**
     * @inheritdoc
     */
    protected function configure() {
        parent::configure();

        $this
            ->setName('repo:set-options')
            ->setDescription('Set options for repositories.')
            ->setHelp('Set some options for the specified repositories.');

        $this->addOption(
            'has-issues',
            'i',
            InputOption::VALUE_OPTIONAL,
            'Enable or disable issues.'
        );

        $this->addOption(
            'has-wiki',
            'w',
            InputOption::VALUE_OPTIONAL,
            'Enable or disable projects.'
        );

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
        $io = new SymfonyStyle($input, $output);
        $logger = new ConsoleLogger($io);

        // Get the specified options.
        $options = [];

        if ($this->isOptionSpecified($input, 'has-issues')) {
            $options['has_issues'] = $this->getBoolOption($input, 'has-issues');
        }

        if ($this->isOptionSpecified($input, 'has-wiki')) {
            $options['has_wiki'] =  $this->getBoolOption($input, 'has-wiki');
        }

        if ($this->isOptionSpecified($input, 'has-projects')) {
            $options['has_projects'] =  $this->getBoolOption($input, 'has-projects');
        }

        if (!$options) {
            $logger->error('Specify at least one option to change.');
        }

        // Get the repositories to update.
        $repositories = $this->getRepositories($input, $logger);
        $updated = 0;

        // Update the repositories who's options are different.
        $handler = new RepositoriesHandler($this->client);

        foreach ($repositories as $repository)
        {
            foreach ($options as $option => $value) {
                if ($repository[$option] !== $value) {
                    $handler->updateRepository(
                        $repository['owner']['login'],
                        $repository['name'],
                        $options
                    );

                    $updated++;

                    continue 2;
                }
            }
        }

        $logger->notice(sprintf('Updated %d repositories.', $updated));
    }

    /**
     * Check if an option has been specified.
     *
     * @param InputInterface $input
     *   The input interface.
     * @param string $name
     *   The option name.
     *
     * @return bool
     *   TRUE if the option was specified.
     */
    protected function isOptionSpecified(InputInterface $input, $name) {
        if ($input->hasParameterOption('--' . $name)) {
            return TRUE;
        }

        $option = $this->getDefinition()->getOption($name);
        $name = $option->getShortcut();

        return NULL !== $name && $input->hasParameterOption('-' . $name);
    }

    /**
     * Get a boolean value.
     *
     * @param InputInterface $input
     *   The input interface.
     * @param string $name
     *   The option name.
     * @param bool $default
     *   Default value.
     *
     * @return bool
     *   The boolean value.
     */
    protected function getBoolOption(InputInterface $input, $name, $default = TRUE) {
        $value = $input->getOption($name);

        if (NULL === $value) {
            return $default;
        }

        return in_array($value, ['1', 'true', 'yes'], TRUE);
    }
}
