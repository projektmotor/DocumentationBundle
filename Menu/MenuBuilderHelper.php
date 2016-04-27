<?php

namespace Pm\DocumentationBundle\Menu;

use Knp\Menu\ItemInterface;
use Pm\DocumentationBundle\Markdown\Parser;
use Pm\DocumentationBundle\Markdown\Reader\MarkdownReaderInterface;
use Pm\DocumentationBundle\Menu\Cache\MenuCacheInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * MenuBuilderHelper
 *
 * @author Soeren Helbig <soeren.helbig@projektmotor.de>
 * @copyright ProjektMOTOR GmbH 2015
 */
class MenuBuilderHelper
{
    /** @var Parser */
    private $mardownParser;

    /** @var MarkdownReaderInterface */
    private $markdownReader;

    /** @var MenuCacheInterface */
    private $cache;

    /**
     * @param Parser $markdownParser
     * @param MarkdownReaderInterface $markdownReader
     * @param MenuCacheInterface $cache
     */
    public function __construct(
        Parser $markdownParser,
        MarkdownReaderInterface $markdownReader,
        MenuCacheInterface $cache
    ) {
        $this->mardownParser = $markdownParser;
        $this->markdownReader = $markdownReader;
        $this->cache = $cache;
    }

    /**
     * @param ItemInterface $rootItem
     * @param string $locale
     */
    public function buildDocumentationMenu(ItemInterface $rootItem, $locale = 'en')
    {
        try {
            $chilItems = $this->cache->read($locale);

            /** @var ItemInterface $childItem */
            foreach ($childItems as $childItem) {
                $rootItem->addChild($childItem);
            }
        } catch (FileNotFoundException $e) {
            $markdown = $this->markdownReader->getTocMarkdown($locale);
            $htmlToc = $this->buildHtmlToc($markdown);

            $this->buildMenuItems($htmlToc, $rootItem, $locale);

            $this->cache->write($rootItem->getChildren(), $locale);
        }
    }

    /**
     * @param string $markdown
     * @return string
     */
    private function buildHtmlToc($markdown)
    {
        if (strlen($markdown) === 0) {
            return '';
        }

        return str_replace("\n", '', $this->mardownParser->transform($markdown));
    }

    /**
     * @param string $htmlToc
     * @param ItemInterface $rootItem
     * @param string $locale
     */
    private function buildMenuItems($htmlToc, ItemInterface $rootItem, $locale)
    {
        $crawler = new Crawler($htmlToc);

        return $this->buildMenuForList(
            $crawler->filter('html > body > ul')->getNode(0),
            $rootItem,
            $locale
        );

    }

    /**
     * @param \DOMElement $listNode
     * @param ItemInterface $rootItem
     * @param string $locale
     */
    private function buildMenuForList(\DOMElement $listNode, ItemInterface $rootItem, $locale)
    {
        /** @var \DOMElement $child */
        foreach ($listNode->childNodes as $child) {
            if ($child->nodeName === '#text') {
                continue;
            }

            $linkNode = $child->childNodes->item(0);

            if (!$linkNode->attributes || !$linkNode->attributes->getNamedItem('href')) {
                continue;
            }

            $nodeContent = utf8_decode($linkNode->textContent);

            $rootItem->addChild(
                $nodeContent,
                array(
                    'uri' => $linkNode->attributes->getNamedItem('href')->nodeValue,
                    'attributes' => array(
                        'title' => utf8_decode($linkNode->attributes->getNamedItem('title')->nodeValue)
                    )
                )
            );

            if ($child->childNodes->item(1)) {
                $this->buildMenuForList(
                    $child->childNodes->item(1),
                    $rootItem[$nodeContent],
                    $locale
                );
            }
        }
    }
}
