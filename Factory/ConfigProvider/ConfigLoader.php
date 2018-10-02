<?php declare(strict_types = 1);

namespace lkovace18\EntityFactoryBundle\Factory\ConfigProvider;

class ConfigLoader
{
    /** @var string */
    private $directory;

    public function __construct(string $directory)
    {
        $this->directory = $directory;
    }

    public function getFiles(): array
    {
        $files = [];
        if ($handle = opendir($this->directory)) {
            while (false !== ($entry = readdir($handle))) {
                if (substr($entry, -3, 3) == 'yml') {
                    $files[] = $this->directory . DIRECTORY_SEPARATOR . $entry;
                }
            }
        }

        return $files;
    }
}
