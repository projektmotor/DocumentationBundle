<?php

namespace Pm\DocumentationBundle\Menu;

use Knp\Menu\FactoryInterface;
use Pm\DocumentationBundle\Menu\MenuBuilderHelper;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * SubMenuBuilder
 *
 * @author Soeren Helbig <soeren.helbig@projektmotor.de>
 * @copyright ProjektMOTOR GmbH 2015
 */
class DocMenuBuilder
{
    private $factory;

    private $subMenuBuilder;

    /**
     * @param FactoryInterface $factory
     * @param MenuBuilderHelper $subMenuBuilder
     */
    public function __construct(FactoryInterface $factory, MenuBuilderHelper $subMenuBuilder)
    {
        $this->factory = $factory;
        $this->subMenuBuilder = $subMenuBuilder;
    }

    public function createDocMenu(RequestStack $requestStack)
    {
        $menu = $this->factory->createItem('root');

        $this->subMenuBuilder->buildDocumentationMenu($menu);

        return $menu;
    }
}
