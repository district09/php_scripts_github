<?php

namespace DigipolisGent\Github\Core\Filter;

/**
 * Filter by one or more repository types.
 *
 * @package DigipolisGent\Github\Core\Filter
 */
class Type extends Pattern
{
    /**
     * Supported types.
     *
     * @var string
     */
    // TODO: Put this in configuration
    private $types = array(
        'drupal_module' => '/^drupal_module/',
        'drupal_profile' => '/^drupal_profile/',
        'drupal_site' => '/^drupal_site/',
        'drupal_theme' => '/^drupal_theme/',
        'drupal8_site' => '/^drupal8_site/',
        'drupal8_module' => '/^drupal_module/',
        'php_package' => '/^php_package/',
    );

    /**
     * Pass the types to filter by during creation.
     *
     * @param array $types
     *   Types to filter by.
     */
    public function __construct(array $types)
    {
        $patterns = array_intersect_key(
            $this->types,
            array_flip($types)
        );
        parent::__construct(array_values($patterns));
    }

    /**
     * Allowed types.
     *
     * @return array.
     */
    public static function allowedTypes()
    {
        $filter = new static(array());
        return array_keys($filter->types);
    }
}
