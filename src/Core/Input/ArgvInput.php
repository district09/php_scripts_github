<?php

namespace DigipolisGent\Github\Core\Input;

use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;

/**
 *
 * @package DigipolisGent\Github\Core\Input
 */
class ArgvInput implements InputInterface
{
    /**
     * The input definition.
     *
     * @var InputDefinition
     */
    private $definition;

    /**
     * The actual input object.
     *
     * @var InputInterface
     */
    private $input;

    /**
     * Class constructor.
     *
     * @param InputDefinition $definition
     *   The input definition.
     * @param InputInterface $input
     *   The actual input object.
     */
    public function __construct(InputDefinition $definition, InputInterface $input)
    {
        $this->definition = $definition;
        $this->input = $input;
    }

    /**
     * @inheritdoc
     */
    public function getFirstArgument()
    {
        return $this->input->getFirstArgument();
    }

    /**
     * @inheritdoc
     */
    public function hasParameterOption($values, $onlyParams = false)
    {
        return $this->input->hasParameterOption($values, $onlyParams);
    }

    /**
     * @inheritdoc
     */
    public function getParameterOption($values, $default = false, $onlyParams = false)
    {
        return $this->input->getParameterOption($values, $default, $onlyParams);
    }

    /**
     * @inheritdoc
     */
    public function bind(InputDefinition $definition)
    {
        $this->input->bind($definition);
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        $this->input->validate();
    }

    /**
     * @inheritdoc
     */
    public function getArguments()
    {
        return $this->input->getArguments();
    }

    /**
     * @inheritdoc
     */
    public function getArgument($name)
    {
        return $this->input->getArgument($name);
    }

    /**
     * @inheritdoc
     */
    public function setArgument($name, $value)
    {
        $this->input->setArgument($name, $value);
    }

    /**
     * @inheritdoc
     */
    public function hasArgument($name)
    {
        return $this->input->hasArgument($name);
    }

    /**
     * @inheritdoc
     */
    public function getOptions()
    {
        return $this->input->getOptions();
    }

    /**
     * @inheritdoc
     */
    public function getOption($name)
    {
        return $this->input->getOption($name);
    }

    /**
     * Get a boolean option.
     *
     * @param string $name
     *   The option name.
     * @param bool $trueIfNull
     *   Return true if the option doesn't have a value.
     *
     * @return bool
     */
    public function getBoolOption($name, $trueIfNull = true)
    {
        $true = ['1', 'true', 'y', 'yes'];

        if ($trueIfNull) {
            $true[] = null;
        }

        return in_array($this->input->getOption($name), $true, true);
    }

    /**
     * @inheritdoc
     */
    public function setOption($name, $value)
    {
        $this->input->setOption($name, $value);
    }

    /**
     * @inheritdoc
     */
    public function hasOption($name)
    {
        return $this->input->hasOption($name);
    }

    /**
     * Check if an option has been specified.
     *
     * @param string $name
     *   The option name.
     *
     * @return bool
     *
     * @throws InvalidOptionException
     */
    public function isOptionSpecified($name)
    {
        if (!$this->definition->hasOption($name)) {
            throw new InvalidOptionException(sprintf("The %s option doesn't exists.", $name));
        }

        if ($this->input->hasParameterOption('--' . $name)) {
            return true;
        }

        $option = $this->definition->getOption($name);
        $name = $option->getShortcut();

        return null !== $name && $this->input->hasParameterOption('-' . $name);
    }

    /**
     * @inheritdoc
     */
    public function isInteractive()
    {
        return $this->input->isInteractive();
    }

    /**
     * @inheritdoc
     */
    public function setInteractive($interactive)
    {
        $this->input->setInteractive($interactive);
    }
}
