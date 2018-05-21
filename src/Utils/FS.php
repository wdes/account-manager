<?php
declare(strict_types=1);

namespace AccountManager\Utils;

class FS {
    // Source : stackoverflow.com/a/7288067/5155484
    public static function rmdir_recursive(string $dir) {
        foreach(scandir($dir) as $file) {
            if ('.' === $file || '..' === $file) continue;
            if (is_dir("$dir/$file")) \AccountManager\Utils\FS::rmdir_recursive("$dir/$file");
            else unlink("$dir/$file");
        }
        rmdir($dir);
    }
}
