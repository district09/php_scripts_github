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
     * Checks if the given string passes the filter.
     *
     * @param string $value
     *
     * @return bool
     */
    public function passes($value);
}
