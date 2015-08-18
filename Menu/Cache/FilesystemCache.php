<?php

namespace Pm\DocumentationBundle\Menu\Cache;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * FilesystemCache
 *
 * @author Soeren Helbig <soeren.helbig@projektmotor.de>
 * @copyright ProjektMOTOR GmbH 2015
 */
class FilesystemCache implements MenuCacheInterface
{
    const CACHE_FILE = 'menu.serialized';

    /** @var string */
    private $cacheDir;

    /** @var MenuCacheTransformer */
    private $menuCacheTransformer;

    /**
     * @param $cacheDir
     * @param MenuCacheTransformer $menuCacheTransformer
     */
    public function __construct($cacheDir, MenuCacheTransformer $menuCacheTransformer)
    {
        $this->cacheDir = $cacheDir;
        $this->menuCacheTransformer = $menuCacheTransformer;
    }

    /**
     * @inheritdoc
     */
    public function write(array $children)
    {
        if (!is_dir($this->getCacheDir())) {
            mkdir($this->getCacheDir(), 0777, true);
        }

        file_put_contents(
            $this->getCacheDir() . self::CACHE_FILE,
            serialize(
                $this->menuCacheTransformer->transformToCacheData($children)
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function read()
    {
        if (!file_exists($this->getCacheDir() . self::CACHE_FILE)) {
            throw new FileNotFoundException();
        }

        $cashedData = unserialize(
            file_get_contents(
                $this->getCacheDir() . self::CACHE_FILE
            )
        );

        return $this->menuCacheTransformer->transformToMenuData($cashedData);
    }

    /**
     * @return string
     */
    private function getCacheDir()
    {
        return sprintf(
            '%s/pm/DocumentationBundle/',
            $this->cacheDir
        );
    }
}
