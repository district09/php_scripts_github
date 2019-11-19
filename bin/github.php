<?php
/**
 * Main Github CLI application.
 */

// Autoloader.
$autoloadLocations = [
    getcwd() . '/vendor/autoload.php',
    getcwd() . '/../../autoload.php',
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../../autoload.php',
];
$loaded = false;
foreach ($autoloadLocations as $autoload) {
    if (is_file($autoload)) {
        require_once($autoload);
        $loaded = true;
    }
}
if (!$loaded) {
    fwrite(STDERR,
        'You must set up the project dependencies, run the following commands:' . PHP_EOL .
        'curl -s http://getcomposer.org/installer | php' . PHP_EOL .
        'php composer.phar install' . PHP_EOL
    );
    exit(1);
}

use DigipolisGent\Github\Application;
use DigipolisGent\Github\Core\Command\Branch\ProtectCommand;
use DigipolisGent\Github\Core\Command\Repo\ListCommand;
use DigipolisGent\Github\Core\Command\Repo\SetDefaultBranchCommand;
use DigipolisGent\Github\Core\Command\Repo\SetFeaturesCommand;
use DigipolisGent\Github\Composer\Command\UsageCommand as ComposerUsageCommand;
use DigipolisGent\Github\MakeFile\Command\UsageCommand as MakeFileUsageCommand;

// Create a new application.
$application = new Application();

// Register commands.
$application->add(new ListCommand());
$application->add(new SetFeaturesCommand());
$application->add(new SetDefaultBranchCommand());
$application->add(new ProtectCommand());
$application->add(new MakeFileUsageCommand());
$application->add(new ComposerUsageCommand());

// Run the application.
$application->run();
