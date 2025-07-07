<?php
declare (strict_types = 1);
/**
 * This file is part of Scaleum Framework.
 *
 * (C) 2009-2025 Maxim Kirichenko <kirichenko.maxim@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Common\Helpers;

/**
 * QueryStringHelper
 *
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 */
class QueryStringHelper {
    public static function buildQuery(array $baseParams): string {
        $maskedParams = [];

        // Возможные маскирующие имена
        $fakeKeys = ['v', 't', 's', 'k', 'x', 'r', 'd', 'l'];
        shuffle($fakeKeys);

        // Кол-во маскирующих параметров: от 1 до 3
        $fakeCount = random_int(1, 3);

        foreach (array_slice($fakeKeys, 0, $fakeCount) as $fakeKey) {
            // Пропускаем, если ключ уже есть в основе
            if (array_key_exists($fakeKey, $baseParams)) {
                continue;
            }

            // Генерируем случайное значение
            $maskedParams[$fakeKey] = self::randomValue();
        }

        // Объединяем
        $combined = array_merge($baseParams, $maskedParams);

        // Перемешиваем пары ключ => значение
        $shuffled = self::shuffleAssoc($combined);

        return http_build_query($shuffled);
    }

    protected static function randomValue(): string {
        // Пример — случайная строка длиной 6–10 символов
        $length = random_int(6, 10);
        return bin2hex(random_bytes(intdiv($length, 2)));
    }
    
    protected static function shuffleAssoc(array $array): array {
        $keys = array_keys($array);
        shuffle($keys);

        $shuffled = [];
        foreach ($keys as $key) {
            $shuffled[$key] = $array[$key];
        }

        return $shuffled;
    }
}
/** End of QueryStringHelper **/