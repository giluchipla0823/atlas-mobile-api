<?php
use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;

$services = include config_path('services.php');

$domains = $services['domains'];

if(isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST']) ){
    $domain = $_SERVER['HTTP_HOST'];

    $env = null;

    switch($domain) {
        case $domains['local']:
            $env = '.env.local';
            break;
        case $domains['test']:
            $env = '.env.test';
            break;
        case $domains['qa']:
            $env = '.env.qa';
            break;
        case $domains['pre']:
            $env = '.env.pre';
            break;
        case $domains['pro']:
            $env = '.env.production';
            break;
    };

    if ($env) {
        $dotenv = Dotenv::createImmutable(base_path(), $env);

        try {
            $dotenv->load();
        } catch (InvalidPathException $e) {
            // No custom .env file found for this domain

        }
    }
}
