<?php

namespace DigipolisGent\Github\Core\Filter;

/**
 * Filter by repository type.
 *
 * @package DigipolisGent\Github\Core\Filter
 */
class Type extends Pattern
{
    /**
     * Class constructor.
     *
     * @param string $type
     *   Type to filter by.
     */
    public function __construct($type)
    {
        $pattern = preg_quote($type, '#');
        $pattern = '#^' . $pattern . '#';

        parent::__construct($pattern, null);
    }

    /**
     * Get the supported types.
     *
     * @return array
     */
    public static function supportedTypes()
    {
        return [
            'drupal_module',
            'drupal_profile',
            'drupal_site',
            'drupal_theme',
            'drupal8_site',
            'drupal8_module',
            'php_package',
            'symfony_site',
            'laravel_site',
            'generic_site',
        ];
    }
}
