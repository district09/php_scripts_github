<?php

namespace DigipolisGent\Github;

/**
 * Github application.
 *
 * @package DigipolisGent\Github
 */
class Application extends \Symfony\Component\Console\Application
{
    /**
     * @var string
     */
    const NAME = 'Github Console';

    /**
     * @var string
     */
    const VERSION = '0.0.1-dev';

    /**
     * Application constructor.
     */
    public function __construct()
    {
        parent::__construct($this::NAME, $this::VERSION);
    }
}
