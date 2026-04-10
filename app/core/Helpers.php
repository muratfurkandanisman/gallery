<?php

function env_base_url(): string
{
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    if ($scriptDir === '/' || $scriptDir === '\\') {
        return '';
    }
    return rtrim($scriptDir, '/');
}
