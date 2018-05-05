<?php

require 'php/twig-env.php';

$template = $twig->load('compositions.twig.html');
echo $template->render();