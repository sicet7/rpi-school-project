<?php

declare(strict_types=1);

$file = __DIR__ . '/ssl.crt';

if (!file_exists($file)) {
    echo 'Failed to find CRT file' . PHP_EOL;
    exit(1);
}

$handle = fopen($file, "r");
if ($handle) {
    $output = '';
    while (($line = fgets($handle)) !== false) {
        if ($output == '') {
            $output .= 'const char SSL_CA_PEM[] = ';
        }
        $output .= '"' . trim($line) . '\n"' . "\n";
    }
    fclose($handle);
    echo trim($output) . ';';
    echo PHP_EOL;
} else {
    echo 'Failed to open CRT file.' . PHP_EOL;
    exit(1);
}