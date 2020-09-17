<?php

declare(strict_types=1);

$env = [];
$environmentFile = dirname(__DIR__) . '/.env';
if (file_exists($environmentFile)) {
    $handle = fopen($environmentFile, 'r');
    while (($line = fgets($handle)) !== false) {
        $parts = explode('=', $line);
        $key = trim($parts[0]);
        $value = trim($parts[1]);
        if (is_numeric($value)) {
            $env[$key] = (int) $value;
        } else {
            $env[$key] = $value;
        }
    }
    fclose($handle);
}
$env = array_replace($env, $_ENV);
return $env;