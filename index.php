<?php

@include_once __DIR__ . '/vendor/autoload.php';

Kirby::plugin('bnomei/bolt', [
    'options' => [],
    'pageMethods' => [
        'bolt' => function (string $id) {
            return \Bnomei\Bolt::page($id, $this->root());
        },
    ],
]);

if (! function_exists('bolt')) {
    function bolt(string $id)
    {
        return \Bnomei\Bolt::page($id);
    }
}
