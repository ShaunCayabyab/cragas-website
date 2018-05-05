<?php
require_once '../vendor/autoload.php';

$loader = new Twig_Loader_Filesystem('build/views');
$twig = new Twig_Environment($loader, array(
    'cache' => 'cache/views',
));