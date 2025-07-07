<?php

/**
 * This file is part of CargOS.
 *
 * (C) 2009-2024 Maxim Kirichenko <kirichenko.maxim@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Chronolog\LogBook;
use Chronolog\Scriber\FileScriber;
use Chronolog\Scriber\Renderer\StringRenderer;
use Chronolog\Severity;

return [
    // default
    'kernel' => [
        'class'     => LogBook::class,
        'enabled'   => true,
        'track'     => 'kernel',
        'scribes'   => [
            [
                'class'             => FileScriber::class,
                'severity'          => Severity::Debug,
                'renderer'          => [
                    'class'           => StringRenderer::class,
                    'pattern'         => "[%datetime%]: %severity_name% %message% %assets%",
                    'format'          => 'Y-m-d\TH:i:s.vP',
                    'allow_multiline' => false,
                    'include_traces'  => true,
                    'base_path'       => __DIR__ . '/../src',
                    // 'row_max_length' => 128,
                    // 'row_oversize_replacement' => '...',
                ],
                'path'              => __DIR__ . '/../log/' . date('Y/m'),
                'basename'          => 'kernel',
                'size_threshold'    => 1024 * 1000,
                'max_files'         => 7,
                'write_immediately' => false,
                'collaborative'     => true,
            ],
        ],
        'extenders' => [],
    ],
];
