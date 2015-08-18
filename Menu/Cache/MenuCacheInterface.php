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
     * @return mixed
     */
    public function write(array $children);

    /**
     * @return array|null
     */
    public function read();
}
