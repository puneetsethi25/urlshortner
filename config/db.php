<?php

$json = file_get_contents(__DIR__.'/config.json');
$config = json_decode($json);

$connection = new PDO("mysql:dbname={$config->db_name};host={$config->db_host}", "{$config->db_user}", "{$config->db_password}");