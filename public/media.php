<?php

require 'php/twig-env.php';

$template = $twig->load('media.twig.html');
echo $template->render();