<?php

/**
 * Check if a file exists in the include path
 *
 * @version      1.2
 * @author       Aidan Lister <aidan@php.net>
 * @param        string $file The name of the file to look for
 * @return       bool True if the file exists, False if it does not
 */

function file_exists_incpath ($file)
{
    $paths = explode(PATH_SEPARATOR, get_include_path());

    foreach ($paths as $path)
    {
        // Formulate the absolute path
        $fullpath = $path . DIRECTORY_SEPARATOR . $file;

        // Check it
        if (file_exists($fullpath)) {
            return true;
        }
    }

    return false;
}

?>
