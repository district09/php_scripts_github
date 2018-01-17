<?php
/**
 * Main Github CLI application.
 */

// Autoloader.
require __DIR__ . '/../vendor/autoload.php';

use DigipolisGent\Github\Application;
use DigipolisGent\Github\Core\Command\Repo\SetOptionsCommand;
use DigipolisGent\Github\MakeFile\Command\UsageCommand as MakeFileUsageCommand;
use DigipolisGent\Github\Composer\Command\UsageCommand as ComposerUsageCommand;
use DigipolisGent\Github\Core\Command\Repo\ListCommand;

// Create a new application.
$application = new Application();

// Register commands.
$application->add(new ListCommand());
$application->add(new SetOptionsCommand());
$application->add(new MakeFileUsageCommand());
$application->add(new ComposerUsageCommand());

// Run the application.
$application->run();
