<?php

namespace DigipolisGent\Github\Core\Filter;

/**
 * Filter by one or more regular expression patterns.
 *
 * @package DigipolisGent\Github\Core\Filter
 */
class Pattern implements FilterInterface
{
    /**
     * Pattern to filter by.
     *
     * @var string
     */
    private $pattern;

    /**
     * Class constructor
     *
     * @param string $pattern
     *   Pattern to match on.
     * @param string $delimiter
     *   The regex delimiter.
     */
    public function __construct($pattern, $delimiter = '#')
    {
        if (null !== $delimiter && '' !== $delimiter) {
            $pattern = str_replace($delimiter, '\\' . $delimiter, $pattern);
            $pattern = $delimiter . $pattern . $delimiter;
        }

        $this->pattern = $pattern;
    }

    /**
     * @inheritdoc
     */
    public function passes($value)
    {
        return (bool) preg_match($this->pattern, $value);
    }
}
