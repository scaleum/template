<?php

declare (strict_types = 1);

use Scaleum\Core\DependencyInjection\Framework;
use Scaleum\Stdlib\SAPI\Explorer;
use Scaleum\Stdlib\SAPI\SapiMode;

/**
 * This file is part of Scaleum Framework.
 *
 * (C) 2009-2025 Maxim Kirichenko <kirichenko.maxim@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    // имя сервиса => конфигурация
    // где:
    // - имя сервиса - имя сервиса(либо обьект), под которым он будет доступен в ServiceLocator|ServiceManager
    // - конфигурация - строка/массив/обьект с конфигурацией сервиса
    // пример:
    // 'service1' => 'Namespace\Class1'
    // 'service2' => ['class' => Namespace::Class2, 'config' => ['param1' => 'value1', 'param2' => 'value2'], 'dependencies' => [...]]
    // 'service3' => ['class' => Namespace::Class2, 'param1' => 'value1', 'param2' => 'value2']
    // ключевые слова(массива) конфигурации:
    // - class - имя класса сервиса
    // - config - массив с конфигурацией сервиса; не обязательный, параметры конфигурации могут быть указаны в корне конфигурации
    // - dependencies - массив с зависимостями сервиса, не обязательный

    'db'         => [
        'class'  => Scaleum\Storages\PDO\Database::class,
        'config' => [
            'dsn'               => 'mysql:host=localhost:3310;dbname=template',
            'user'              => 'root',
            'password'          => 'C~nZxd6&e!95',
            'multiple_commands' => true,
            'logging'           => true,
            // 'logger_channel'    => 'db',
        ],
    ],
    'session'    => [
        'class'  => Scaleum\Session\DatabaseSession::class,
        'config' => [
            'dependencies'    => [
                'events'   => Framework::SVC_EVENTS,
                'database' => 'db',
            ],
            'name'            => 'SSID',
            'expiration'      => 3600,
            'cookies'         => [
                'encode' => true,
                'salt'   => '80abd3604a1544188c1fae0e9bc16ebf',
            ],
            // autoload - загрузка сервиса в зависимости от режима работы приложения
            'eager'           => Explorer::getTypeFamily() !== SapiMode::CONSOLE,
            // autoDeployment - создание таблицы в БД, если она не существует (только для DatabaseSession)
            'auto_deployment' => false,
        ],
    ],
    'translator' => [
        'class'  => Scaleum\i18n\Translator::class,
        'config' => [
            'localeBase' => __DIR__ . '/../translations',
            'files'      => [
                'default' => [
                    'type'       => 'gettext',
                    'filename'   => 'default.po',
                    'textDomain' => 'default',
                ],
                'auth'    => [
                    'type'       => 'gettext',
                    'filename'   => 'auth.po',
                    'textDomain' => 'auth',
                ],
                'returns' => [
                    'type'       => 'gettext',
                    'filename'   => 'returns.po',
                    'textDomain' => 'default',
                ],
            ],
            'eager'      => true,
        ],
    ],
    'lang'       => [
        'class'  => Application\Common\i18n\LocaleManager::class,
        'config' => [
            'dependencies'  => [
                'translator' => 'translator',
                'session'    => 'session',
            ],
            'locales'       => [
                // iso => [settings]
                'uk_UA' => [
                    'iso'        => 'uk_UA',
                    'isoAliases' => 'uk,ua',
                    'language'   => 'Українська (Україна)',
                    'idiom'      => 'ukrainian',
                ],
                'en_US' => [
                    'iso'        => 'en_US',
                    'isoAliases' => 'en',
                    'language'   => 'English (USA)',
                    'idiom'      => 'english',
                ],
            ],
            'defaultLocale' => 'uk_UA',
            'eager'         => true,
        ],
    ],
];
