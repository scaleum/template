<?php
declare (strict_types = 1);

use Application\Common\Http\Renderers\Plugins\Locale;

return [
    "locations" => [
        /**
         * значения ассоциативных массивов при слиянии перезаписываются(при совпадении ключей)
         * значения индексных массивов при слиянии добавляются
         * см. ArrayHelper::merge(...)
         */
        'views'   => __DIR__ . "/../views",
        'layouts' => __DIR__ . "/../views/layouts",
    ],
    "views"     => [
        "main" => "default.view",
    ],
    "layout"    => "main",
    "plugins" => [
        Locale::class
    ]
];