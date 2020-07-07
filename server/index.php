<?php

require_once __DIR__.'/../config/functions.php';
require_once __DIR__.'/../config/db.php';;

require_once 'shortner.php';

$shortener = new Shortener($config->server_url, $connection);
$shortener->execute();

