# PmDocumentationBundle

Symfony2 Bundle to build simple Markdown based documentations. (thanks to [KnpMarkdownBundle](https://github.com/KnpLabs/KnpMarkdownBundle) & [KnpMenuBundle](https://github.com/KnpLabs/KnpMenuBundle))

## Features

* filesystem based for simple handling (create markdown-file, request uri, ready!)
* generate menus by simply parsing a toc-file (table of content)
* link rewriting: links could be written absolute to documentation root, bundle rewrites these
links to be accessable by public urls

## Dependencies

* [KnpMarkdownBundle](https://github.com/KnpLabs/KnpMarkdownBundle)
* [KnpMenuBundle](https://github.com/KnpLabs/KnpMenuBundle)

## Install

Install package via composer:

```json
...
"projektmotor/documentationbundle" : "~0.1",
...
```

Activate in AppKernel:

```php
public function registerBundles()
{
    $bundles = array(
        // activate bundle in AppKernel
        new Pm\DocumentationBundle\PmDocumentationBundle(),
        
        // if not yet done
        new Knp\Bundle\MenuBundle\KnpMenuBundle(),
        new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
    );
}
```

Include routing:
```yaml
pm_documentation:
    resource: "@PmDocumentationBundle/Resources/config/routing.yml"
    prefix:   /documentation
```

**OPTIONAL** - Set documentation root dir & template (if you do not want to use the default path):
```yaml
# e.g. app/config/config.yml
pm_documentation:
    doc_path:   '/path/to/doc'
    image_path: '/path/to/doc/images'
    view:       'AppBundle::layout.html.twig'
```
* NOTE: the template (view) MUST CONTAIN a block called *content* where the parsed markdown is rendered in.

## Usage

### Directory Structure

* let`s say your documentation-root is at **/my/doc**
* the first level subdirectories divide different languages from each other
  *  /my/doc/en
  *  /my/doc/de
  *  ...
*  every dir behind the language-dirs **MUST** contain an *index.md*
  *  /my/doc/en/index.md
  *  /my/doc/en/chapter_1/index.md
  *  ...
*  the language-dirs **SHOULD** contain a toc.md (depending on your need of a menu)
  *  /my/doc/en/toc.md
  *  /my/doc/de/toc.md
  *  ...

### Including the (Sub-) Menu:
* inject menu helper to you Knp MenuBuilder:
```yaml
services:
    app.menu_builder:
        class: AppBundle\Menu\MenuBuilder
        arguments:
            - @knp_menu.factory
            - @pm_documentation.menu_helper
```
```php
class MenuBuilder
{
    private $menuBuilderHelper;
    
    public function __construct(FactoryInterface $factory, MenuBuilderHelper $subMenuBuilder)
    {
        $this->factory = $factory;
        $this->menuBuilderHelper = $subMenuBuilder;
    }
}
```

* add menu (based on toc.md)
```php
/**
 * @param RequestStack $requestStack
 * @return \Knp\Menu\ItemInterface
 */
public function createMainMenu(RequestStack $requestStack)
{
    $menu = $this->factory->createItem('root');
    
    // adding documentation menu as submenu
    $menu->addChild('Documentation', array(...));
    $this->subMenuBuilder->buildDocumentationMenu($menu['Documentation']);
    
    // OR adding as first-level menu
    $this->subMenuBuilder->buildDocumentationMenu($menu);
}
```
