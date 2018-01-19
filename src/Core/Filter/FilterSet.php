<?php

namespace DigipolisGent\Github\Core\Filter;

/**
 * Used to combine multiple filters.
 *
 * @package DigipolisGent\Github\Core\Filter
 */
class FilterSet implements FilterInterface
{
    const OPERATOR_AND = 'AND';
    const OPERATOR_OR = 'OR';
    const OPERATOR_XOR = 'XOR';

    /**
     * The logical operator.
     *
     * @var string
     */
    protected $operator;

    /**
     * The actual filters.
     *
     * @var FilterInterface[]
     */
    protected $filters = [];

    /**
     * The base class of the filters in this set.
     *
     * @var string
     */
    protected $baseClass;

    /**
     * Class constructor.
     *
     * @param $operator
     *   The logical operator.
     */
    public function __construct($operator = self::OPERATOR_AND)
    {
        $this->operator = $operator;
    }

    /**
     * Check if the set is empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->filters);
    }

    /**
     * Add a filter to the set.
     *
     * @param FilterInterface $filter
     *   The filter.
     *
     * @throws \LogicException
     */
    public function addFilter(FilterInterface $filter)
    {
        if (null === $this->baseClass) {
            $this->setBaseClass($filter);
        } elseif ($this->operator === self::OPERATOR_XOR && count($this->filters) === 2) {
            throw new \LogicException('An XOR filter set can only contain 2 filters.');
        } else {
            $base_class = $this->getBaseClass($filter);

            if (null !== $base_class && $base_class !== $this->baseClass) {
                throw new \LogicException('The specified filter doesn not implement the other filters in this set.');
            }
        }

        $this->filters[] = $filter;
    }

    /**
     * Add an array of filters to the set.
     *
     * @param FilterInterface[] $filters
     *   The filters.
     *
     * @throws \LogicException
     */
    public function addFilters(array $filters)
    {
        foreach ($filters as $filter) {
            $this->addFilter($filter);
        }
    }

    /**
     * Recursively set the filter base class.
     *
     * @param FilterInterface $filter
     *   The filter.
     */
    private function setBaseClass(FilterInterface $filter)
    {
        $this->baseClass = $this->getBaseClass($filter);

        if (null !== $this->baseClass) {
            foreach ($this->filters as $item) {
                if ($item instanceof static) {
                    $item->setBaseClass($this);
                }
            }
        }
    }

    /**
     * Get the filter base class.
     *
     * @param FilterInterface $filter
     *   The filter.
     *
     * @return string
     */
    protected function getBaseClass(FilterInterface $filter)
    {
        if ($filter instanceof static) {
            return $filter->baseClass;
        }

        if ($parents = class_parents($filter)) {
            return end($parents);
        }

        return get_class($filter);
    }

    /**
     * @inheritdoc
     */
    public function passes($value)
    {
        switch ($this->operator) {
            case self::OPERATOR_XOR:
                return $this->passesXor($value);

            case self::OPERATOR_OR:
                return $this->passesOr($value);

            default:
                return $this->passesAnd($value);
        }
    }

    /**
     * Checks the filters using an AND.
     *
     * @param mixed $value
     *   The value to check.
     *
     * @return bool
     */
    private function passesAnd($value)
    {
        foreach ($this->filters as $filter) {
            if (!$filter->passes($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks the filters using an OR.
     *
     * @param mixed $value
     *   The value to check.
     *
     * @return bool
     */
    private function passesOr($value)
    {
        if ($this->filters) {
            foreach ($this->filters as $filter) {
                if ($filter->passes($value)) {
                    return true;
                }
            }

            return false;
        }

        return true;
    }

    /**
     * Checks the filters using an XOR.
     *
     * @param mixed $value
     *   The value to check.
     *
     * @return bool
     */
    private function passesXor($value)
    {
        if (count($this->filters) !== 2) {
            return $this->passesOr($value);
        }

        $a = reset($this->filters);
        $b = end($this->filters);

        return $a->passes($value) xor $b->passes($value);
    }
}
