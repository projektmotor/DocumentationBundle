<?php

namespace Pm\DocumentationBundle\Menu\Cache;

use Knp\Menu\ItemInterface;
use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;

/**
 * MenuCacheTransformer
 *
 * @author Soeren Helbig <soeren.helbig@projektmotor.de>
 * @copyright ProjektMOTOR GmbH 2015
 */
class MenuCacheTransformer
{
    /** @var MenuFactory */
    private $menuFactory;

    /**
     * @param MenuFactory $menuFactory
     */
    public function __construct(MenuFactory $menuFactory)
    {
        $this->menuFactory = $menuFactory;
    }

    /**
     * transform menu data into cachable data
     *
     * @param array $children
     * @return array
     */
    public function transformToCacheData(array $children)
    {
        $cacheData = array();

        /** @var MenuItem $childItem */
        foreach ($children as $childItem) {
            $itemCacheData = array(
                'uri' => $childItem->getUri(),
                'name' => $childItem->getName(),
                'title' => $childItem->getAttribute('title')
            );

            if (count($childItem->getChildren()) > 0) {
                $itemCacheData['children'] = $this->transformToCacheData($childItem->getChildren());
            }

            $cacheData[] = $itemCacheData;
        }

        return $cacheData;
    }


    /**
     * transform cached data into menu items
     *
     * @param array $children
     * @param ItemInterface|null $parent
     * @return array
     */
    public function transformToMenuData(array $children, ItemInterface $parent = null)
    {
        $menuChildren = array();

        foreach ($children as $childItem) {
            $menuItem = $this->menuFactory->createItem(
                $childItem['name'],
                array(
                    'uri' => $childItem['uri'],
                    'attributes' => array(
                        'title' => $childItem['title']
                    )
                )
            );

            if (isset($childItem['children'])) {
                $this->transformToMenuData($childItem['children'], $menuItem);
            }

            ($parent)
                ? $parent->addChild($menuItem)
                : $menuChildren[] = $menuItem;
        }

        return $menuChildren;
    }
}
