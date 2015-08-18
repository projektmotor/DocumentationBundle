<?php

namespace Pm\DocumentationBundle\Markdown\Reader\Filesystem;

use Pm\DocumentationBundle\Markdown\Reader\MarkdownReaderInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

/**
 * FilesystemMarkdownReader
 *
 * @author Soeren Helbig <soeren.helbig@projektmotor.de>
 * @copyright ProjektMOTOR GmbH 2015
 */
class FilesystemMarkdownReader implements MarkdownReaderInterface
{
    /** @var string  */
    private $kernelRootDir;

    /** @var string  */
    private $docDir;

    /** @var string  */
    private $imageDir;

    /**
     * @param string $kernelRootDir
     * @param string $docDir
     * @param string $imageDir
     */
    public function __construct($kernelRootDir, $docDir, $imageDir)
    {
        $this->kernelRootDir = $kernelRootDir;
        $this->docDir = $docDir;
        $this->imageDir = $imageDir;
    }

    /**
     * @inheritdoc
     */
    public function getContentMarkdown($locale = 'de', $route = null)
    {
        $basePath = $this->getMarkdownBasePath();
        $targetPath = $this->getTargetPath($basePath.'/'.$locale, $route);

        return file_get_contents($targetPath);
    }

    /**
     * @return string
     */
    private function getMarkdownBasePath()
    {
        return (substr($this->docDir, 0, 1) === '/')
            ? $this->docDir
            : $this->kernelRootDir.'/'.$this->docDir;
    }

    /**
     * @param $localePath
     * @param $route
     * @return mixed
     */
    private function getTargetPath($localePath, $route = null)
    {
        $filePath = ($route)
            ? $localePath . '/' . $route
            : $localePath ;

        if (is_file($filePath . '.md')) {
            $filePath .= '.md';
        } else {
            $filePath .= '/index.md';
        }

        if (!is_file($filePath)) {
            throw new FileNotFoundException($filePath);
        }

        return $filePath;
    }

    /**
     * @inheritdoc
     */
    public function getTocMarkdown($locale = 'de')
    {
        $filePath = $this->getMarkdownBasePath() . '/' . $locale . '/toc.md';

        if (!is_file($filePath)) {
            throw new FileNotFoundException($filePath);
        }

        return file_get_contents($filePath);
    }
}
