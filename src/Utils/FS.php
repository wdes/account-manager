<?php
declare(strict_types = 1);
namespace AccountManager\Utils;

class FS
{

    /**
     * Delete all files and folders in a directory
     *
     * @param string $dir The directory
     * @see stackoverflow.com/a/7288067/5155484
     * @return void
     */
    public static function rmdirRecursive(string $dir): void
    {
        foreach (scandir($dir) as $file) {
            if ('.' === $file || '..' === $file) {
                continue;
            }
            if (is_dir("$dir/$file")) {
                \AccountManager\Utils\FS::rmdirRecursive("$dir/$file");
            } else {
                unlink("$dir/$file");
            }
        }
        rmdir($dir);
    }

}
