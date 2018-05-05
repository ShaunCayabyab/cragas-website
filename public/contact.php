<?php

require 'php/twig-env.php';

$template = $twig->load('contact.twig.html');
echo $template->render();