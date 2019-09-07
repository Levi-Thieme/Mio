<?php
/*
Recursively retrieves all files paths with the given extension.
*/
function getAllFiles($directory, $extension) {
    $names = array_diff(scandir($directory), array(".", ".."));    
    $files = array();
    foreach($names as $name) {
        if (strpos($name, $extension)) {
            $files[] = $directory . DIRECTORY_SEPARATOR  . $name;
        }
        else if(strpos($name, ".") === false){
            $subdirectoryFiles = getAllFiles($directory .DIRECTORY_SEPARATOR  . $name, $extension);
            foreach ($subdirectoryFiles as $file) {
                $files[] = $file;
            }
        }
    }
    return $files;
}
$allFiles = getAllFiles("unit_tests", ".php");
echo json_encode($allFiles);