<?php

require 'php/twig-env.php';

$template = $twig->load('about.twig.html');
echo $template->render();