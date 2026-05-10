<?php

function env_base_url(): string
{
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');

    if ($scriptName === '') {
        return '';
    }

    if (str_contains($scriptName, '/app/views/')) {
        $basePath = preg_replace('#/app/views/.*$#', '', $scriptName);
        return $basePath === '/' ? '' : rtrim((string) $basePath, '/');
    }

    $scriptDir = dirname($scriptName);
    if ($scriptDir === '/' || $scriptDir === '\\') {
        return '';
    }

    return rtrim($scriptDir, '/');
}
