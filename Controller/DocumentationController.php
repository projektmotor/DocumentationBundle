<?php

namespace Pm\DocumentationBundle\Controller;

use Pm\DocumentationBundle\Markdown\Parser;
use Pm\DocumentationBundle\Markdown\Reader\Filesystem\FilesystemMarkdownReader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * DocumentationController
 *
 * @author Soeren Helbig <soeren.helbig@projektmotor.de>
 * @copyright ProjektMOTOR GmbH 2015
 */
class DocumentationController extends Controller
{
    /**
     * @param $locale
     * @param $document
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($locale, $document)
    {
        /** @var FilesystemMarkdownReader $markdownReader */
        $markdownReader = $this->container->get('pm_documentation.markdown.reader.filesystem');
        $markdown = $markdownReader->getContentMarkdown($locale, $document);

        /** @var Parser $parser */
        $parser = $this->container->get('pm_documentation.markdown.parser');

        return $this->render(
            'PmDocumentationBundle:Documentation:index.html.twig',
            array(
                'baseTemplate' => $this->container->getParameter('pm_documentation.view'),
                'content' => $parser->transform($markdown)
            )
        );
    }
}
