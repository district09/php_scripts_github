<?php

namespace DigipolisGent\Github\Core\Filter;

/**
 * Interface FilterInterface.
 *
 * @package DigipolisGent\Github\Core\Filter
 */
interface FilterInterface
{
    /**
     * Checks if the given value passes the filter.
     *
     * @param mixed $value
     *   The value to check.
     *
     * @return bool
     *   True if the filter passes, false otherwise.
     */
    public function passes($value);
}
