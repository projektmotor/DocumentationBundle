<?php

namespace Pm\DocumentationBundle\Markdown;

use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Parser
 *
 * @author Soeren Helbig <soeren.helbig@projektmotor.de>
 * @copyright ProjektMOTOR GmbH 2015
 */
class Parser
{
    /** @var MarkdownParserInterface  */
    private $parser;

    /** @var Router */
    private $router;

    /**
     * @param MarkdownParserInterface $parser
     * @param Router $router
     */
    public function __construct(MarkdownParserInterface $parser, Router $router)
    {
        $this->parser = $parser;
        $this->router = $router;
    }

    /**
     * transform markdown > html
     * - execute KnpMarkdownBundle parser
     * - rewrite link targets
     *
     * @param string $markdown
     * @return mixed
     */
    public function transform($markdown)
    {
        $html = $this->parser->transformMarkdown($markdown);

        return $this->replaceLinks($html);
    }

    /**
     * @param string $html
     * @return string
     */
    private function replaceLinks($html)
    {
        return  preg_replace_callback(
            '/href=("\/.*")\s/',
            function ($hits) {
                $uri = $hits[1];
                $uriParts = explode('/', $uri);

                $locale = $uriParts[1];

                $document = substr($uri, strpos($uri, $locale) + strlen($locale) + 1);

                if (strpos($document, '.md') === strlen($document) - 4) {
                    $document = substr($document, 0, strlen($document) - 4);

                    if (strpos($document, 'index') === strlen($document) - 5) {
                        $document = substr($document, 0, strlen($document) - 6);
                    }
                }

                return sprintf(
                    'href="%s"',
                    $this->router->generate(
                        'pm_documentation_homepage',
                        array(
                            'locale' => $locale,
                            'document' => $document
                        )
                    )
                );
            },
            $html
        );
    }
}

