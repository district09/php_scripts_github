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
     * Patterns to filter by.
     *
     * @var array
     */
    private $patterns = [];

    /**
     * Pass the patterns to filter by during creation.
     *
     * @param array $patterns
     */
    public function __construct(array $patterns)
    {
        $this->patterns = $patterns;
    }

    /**
     * Check if a string passes the filters.
     *
     * @param string $value
     *
     * @return bool
     */
    public function passes($value)
    {
        foreach ($this->patterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return TRUE;
            }
        }

        return FALSE;
    }
}
