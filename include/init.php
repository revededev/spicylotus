<?php
// echo 'inside_init<br>';

session_start();
// require 'config.php';


// require_once "../myTools.php";


//initialisation des class
// function classAutoload(UploadException) {
//     $paths = array('/model/');
//     echo 'inside init after classAutoLoad, paths :<br>';
//     printr($paths);

//     $extension = '.class.php';
//     // $className = strtolower($className);
//     $className = str_replace('\\', '/', $className);

//     foreach($paths as $path) {
//         $fullPath = get_stylesheet_directory_uri().$path . $className . $extension;
//         echo'Full paths : '.$fullPath.'<br>';
        
//         if(file_exists($fullPath)) {
//             require_once($fullPath);
//         }
//     } 
// }
// spl_autoload_register('classAutoload');
?>