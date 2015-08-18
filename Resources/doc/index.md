# PmDocumentationBundle

Symfony2 Bundle to build simple Markdown based documentations.

## Features

* generate menus by simply parsing a toc-file (table of content), which is
(of course) written in markdown
* links could be written absolute to documentation root, bundle rewrites these
links to be accessable by public urls
* filesystem based for simple handling (create markdown-file, link it, ready!)

## Dependencies

* [KnpMarkdownBundle](https://github.com/KnpLabs/KnpMarkdownBundle)
* [KnpMenuBundle](https://github.com/KnpLabs/KnpMenuBundle)

## Installation

Install package via composer:

```json
...
"pm/documentation-bundle": "~0.1",
...
```

Activate in AppKernel:

```php
public function registerBundles()
{
    $bundles = array(
        //...
        new Pm\DocumentationBundle\PmDocumentationBundle(),
        //...
    );
}
```

[OPTIONAL] Set path to documentation files:
```yaml
pm_documentation:
    doc_path: 'doc'
    image_path: 'doc/images'
```