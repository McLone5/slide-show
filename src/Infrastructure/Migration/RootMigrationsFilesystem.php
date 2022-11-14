<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration;

use Kaliop\eZMigrationBundle\API\Value\MigrationDefinition;
use Kaliop\eZMigrationBundle\Core\Loader\Filesystem;
use RuntimeException;

/**
 * Same as original \Kaliop\eZMigrationBundle\Core\Loader\Filesystem
 * BUT making the "migration folder" at the root of the project ("/migrations")
 */
final class RootMigrationsFilesystem extends Filesystem
{
    /**
     * @param string[] $paths either dir names or file names
     * @param bool $returnFilename return either the
     * @return MigrationDefinition[]|string[] migrations definitions. key: name, value: contents of the definition,
     *                                        as string or file path
     */
    protected function getDefinitions(array $paths = array(), $returnFilename = false): array
    {
        // if no paths defined, we look in default paths
        if (empty($paths)) {
            $path = $this->kernel->getProjectDir() . '/migrations';
            if (is_dir($path)) {
                $paths[] = $path;
            }

            foreach ($this->kernel->getBundles() as $bundle) {
                $path = $bundle->getPath() . "/" . $this->versionDirectory;
                if (is_dir($path)) {
                    $paths[] = $path;
                }
            }
        }

        $definitions = array();
        foreach ($paths as $path) {
            if (is_file($path)) {
                $definitions[basename($path)] = $returnFilename ? $path : new MigrationDefinition(
                    basename($path),
                    $path,
                    (string)file_get_contents($path)
                );
            } elseif (is_dir($path)) {
                foreach (new \DirectoryIterator($path) as $file) {
                    if ($file->isFile()) {
                        $definitions[$file->getFilename()] =
                            $returnFilename ? (string)$file->getRealPath() : new MigrationDefinition(
                                $file->getFilename(),
                                $file->getRealPath(),
                                (string)file_get_contents($file->getRealPath())
                            );
                    }
                }
            } else {
                throw new RuntimeException("Path '$path' is neither a file nor directory");
            }
        }
        ksort($definitions);

        return $definitions;
    }
}
