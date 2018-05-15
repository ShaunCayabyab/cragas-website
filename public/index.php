<?php

require 'php/twig-env.php';

$template = $twig->load('index.twig.html');
echo $template->render([
    'featured_works' => [
        'fatal-attraction' => [
            'title'     => 'Fatal Attraction',
            'subtitle'  => null,
            'thumbnail' => null,
            'iframe'    => '',
        ],
        'soundings-surf-documentary' => [
            'title'     => 'Soundings Surf Documentary',
            'subtitle'  => null,
            'thumbnail' => null,
            'iframe'    => '<iframe width="560" height="315" src="https://www.youtube.com/embed/r3J90jJycII?controls=0&amp;showinfo=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>',
        ],
    ],
]);