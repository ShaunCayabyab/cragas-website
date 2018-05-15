<?php

require 'php/twig-env.php';

$template = $twig->load('media.twig.html');
echo $template->render([
	'media_works' => [
		'fatal-attraction' => [
            "title"     => 'Fatal Attraction',
            'subtitle'  => '',
            "thumbnail" => null,
        ],
		'soundings-surf-documentary-video' => [
            "title"     => 'Soundings Surf Documentary',
            'subtitle'  => 'Video',
            "thumbnail" => 'build/img/thumbnails/sounds-surfing.jpg',
        ],
        'soundings-surf-documentary' => [
            "title"     => 'Soundings Surf Documentary',
            'subtitle'  => 'Playlist',
            "thumbnail" => null,
        ],
        'the-cookbook-video' => [
            "title"     => 'The Cookbook',
            'subtitle'  => 'Video',
            "thumbnail" => 'build/img/thumbnails/the-cookbook.jpg',
        ],
        'the-cookbook' => [
            "title"     => 'The Cookbook',
            'subtitle'  => 'Playlist',
            "thumbnail" => null,
        ],
	]
]);