<?php

return [
    'template_directory' => (function () {
        $dir = base_path('resources/emails');
        return stripos(PHP_OS_FAMILY, 'Windows') !== false
            ? 'file:///' . str_replace('\\', '/', $dir) . '/'
            : $dir . '/';
    })(),
    'node_path' => null,
    'tsx_path' => str_replace('\\', '/', base_path('node_modules/tsx/dist/cli.mjs')),
];