<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Markdown Pages Path
    |--------------------------------------------------------------------------
    |
    | This value determines the path where Markdown pages are stored.
    | This path is used by the PageRepository to read and write Markdown files.
    | You can change this to any location that suits your needs.
    |
    */

    'pages_path' => env('MARKDOWN_PAGES_PATH', resource_path('pages')),

    /*
    |--------------------------------------------------------------------------
    | Default Storage Disk for Pages
    |--------------------------------------------------------------------------
    |
    | This value determines which storage disk will be used for storing pages.
    | By default, it uses the 'pages' disk defined in filesystems.php.
    | You can change this to any disk defined in filesystems.php.
    |
    */

    'default_disk' => env('PAGES_DISK', 'pages'),

];
