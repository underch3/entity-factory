<?php

namespace lkovace18\EntityFactoryBundle\Factory\ConfigProvider;

class ConfigLoader
{
    /**
     * @var array
     */
    private $directories;

    public function __construct(array $directories)
    {
        $this->directories = $directories;
    }

    public function getFiles()
    {
        $files = [];

        foreach ($this->directories as $directory) {
            if ($handle = opendir($directory)) {
                while (false !== ($entry = readdir($handle))) {
                    if (substr($entry, -3, 3) == 'yml') {
                        $files[] = $directory . DIRECTORY_SEPARATOR . $entry;
                    }
                }
            }
        }

        return $files;
    }
}
