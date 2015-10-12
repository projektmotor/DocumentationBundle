<?php

namespace Pm\DocumentationBundle\Menu\Cache;

use Knp\Menu\ItemInterface;

/**
 * MenuCacheInterface
 *
 * @author Soeren Helbig <soeren.helbig@projektmotor.de>
 * @copyright ProjektMOTOR GmbH 2015
 */
interface MenuCacheInterface
{
    /**
     * @param array $children
     * @param string $locale
     * @return mixed
     */
    public function write(array $children, $locale = 'en');

    /**
     * @param string $locale
     * @return array|null
     */
    public function read($locale = 'en');
}
