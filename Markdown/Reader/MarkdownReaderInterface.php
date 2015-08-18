<?php

namespace Pm\DocumentationBundle\Markdown\Reader;

/**
 * MarkdownReaderInterface
 *
 * @author Soeren Helbig <soeren.helbig@projektmotor.de>
 * @copyright ProjektMOTOR GmbH 2015
 */
interface MarkdownReaderInterface
{
    /**
     * @param string $locale
     * @param string|null $route
     * @return string
     */
    public function getContentMarkdown($locale = 'de', $route = null);

    /**
     * @param string $locale
     * @return string
     */
    public function getTocMarkdown($locale = 'de');
}
