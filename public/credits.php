<?php

require 'php/twig-env.php';

$template = $twig->load('credits.twig.html');
echo $template->render();