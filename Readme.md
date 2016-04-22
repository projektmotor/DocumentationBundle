# PmDocumentationBundle

Symfony2 Bundle to build simple Markdown based documentations (thanks to [KnpMarkdownBundle](https://github.com/KnpLabs/KnpMarkdownBundle) & [KnpMenuBundle](https://github.com/KnpLabs/KnpMenuBundle)).

Markdown syntax: [cheatsheet](https://guides.github.com/features/mastering-markdown)

## Features

* filesystem based for simple handling (create markdown-file, request uri, ready!)
* generate menus (KnpMenu) by simply parsing a toc-file (table of content)
* link rewriting: links could be written absolute to documentation root, bundle rewrites these
links to be accessable by public urls
* image url rewriting: bundle is publishing the images to the configured path and rewrites urls, similar to links

## Dependencies

* [KnpMarkdownBundle](https://github.com/KnpLabs/KnpMarkdownBundle)
* [KnpMenuBundle](https://github.com/KnpLabs/KnpMenuBundle)

## Install

* Install package via composer:

```json
...
"projektmotor/documentationbundle" : "~0.1",
...
```

* Activate in AppKernel:

```php
// app/AppKernel.php

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

* Include routing:
```yaml
# e.g. app/config/routing.yml
pm_documentation:
    resource: "@PmDocumentationBundle/Resources/config/routing.yml"
    prefix:   /documentation
```

* **OPTIONAL** - Set documentation root dir & template (if you do not want to use the default path):
```yaml
# e.g. app/config/config.yml
pm_documentation:
    doc_path:   '/path/to/doc'
    image_dest: '/web/images/doc'
    view:       'AppBundle::layout.html.twig'
```
  * NOTE: the template (view) MUST CONTAIN a block called *content* where the parsed markdown is rendered in.
  * NOTE: image destination path must be writeable by the web server user (e.g. www-data)

## Usage

### Directory Structure

* let`s say your documentation-root is at **/my/doc**
* first level subdirectories divide different languages from each other
  *  /my/doc/en
  *  /my/doc/de
  *  ...
*  every dir behind the language-dirs **MUST** contain an **index.md** (see [Requesting a Page](#requesting-a-page))
  *  /my/doc/en/index.md
  *  /my/doc/en/chapter_1/index.md
  *  ...
*  the language-dirs **SHOULD** contain a **toc.md** (depending on your need of a menu)
  *  /my/doc/en/toc.md
  *  /my/doc/de/toc.md
  *  ...

### Requesting a Page

Assuming you want to render the markdown file `/path/to/doc/en/chapter_1/first-steps.md` in your browser, just call `http://example.com/documentation/en/chapter_1/first-steps`.

Structure:
* `documentation` : the route prefix (could be set in routing)
* `en` : the locale
* `chapter_1/first_steps` : path to markdown file

If you request a folder instead of specific markdown-file, the `index.md` file of the folder is used. Requesting `http://example.com/documentation/en/chapter_1` would end up in rendering the `/path/to/doc/en/chapter_1/index.md`

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

class MenuBuilder
{
  // ....
  public function createMainMenu(RequestStack $requestStack)
  {
      $menu = $this->factory->createItem('root');

      // adding documentation menu as submenu
      $menu->addChild('Documentation', array(...));
      $this->subMenuBuilder->buildDocumentationMenu($menu['Documentation']);

      // OR adding as first-level menu
      $this->subMenuBuilder->buildDocumentationMenu($menu);
  }
  
  // ....
}
```
