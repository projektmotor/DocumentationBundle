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

    /** @var String */
    private $appRoot;

    /** @var String */
    private $docPath;

    /** @var String */
    private $imageDest;

    /**
     * @param MarkdownParserInterface $parser
     * @param Router $router
     * @param String $appRoot
     * @param String $docPath
     * @param String $imageDest
     */
    public function __construct(MarkdownParserInterface $parser, Router $router, $appRoot, $docPath, $imageDest)
    {
        $this->parser = $parser;
        $this->router = $router;
        $this->appRoot = $appRoot;
        $this->docPath = $docPath;
        $this->imageDest = $imageDest;
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
        $html = $this->replaceLinks($html);

        $test = $this->replaceImages($html);

        return $this->replaceImages($html);
    }

    /**
     * @param string $html
     * @return string
     */
    private function replaceLinks($html)
    {
        return  preg_replace_callback(
//            '/href=("\/.*")[\s>]/',
            '/href=("\/.*")\s?/U',
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

                $test = sprintf(
                    'href="%s" ',
                    $this->router->generate(
                        'pm_documentation_homepage',
                        array(
                            'locale' => $locale,
                            'document' => $document
                        )
                    )
                );;

                return sprintf(
                    'href="%s" ',
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

    /**
     * @param string $html
     * @return string
     */
    private function replaceImages($html)
    {
        return  preg_replace_callback(
            '/!\/.*!/',
            function ($hits) {
                $imagePath = substr($hits[0], 1, strlen($hits[0]) - 2);
                $uriParts = explode('/', $imagePath);
                $filename = $uriParts[count($uriParts) - 1];

                $destPath = $this->appRoot . '/' . $this->imageDest . '/' . $filename;

                if (!file_exists($destPath)) {
                    if (!is_dir($this->appRoot . '/' . $this->imageDest)) {
                        mkdir($this->appRoot . '/' . $this->imageDest, 0777, true);
                    }

                    try {
                        copy(
                            $this->appRoot . '/' . $this->docPath . $imagePath,
                            $destPath
                        );
                    } catch (\Exception $e) {}
                }

                $webPath = '/'.substr($destPath, strpos($destPath, '/web/') + 5);

                return sprintf(
                    '<img src="%s"/>',
                    $webPath
                );
            },
            $html
        );
    }
}

