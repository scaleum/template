<?php

declare(strict_types=1);
/**
 * This file is part of Scaleum Application.
 *
 * (C) 2009-2025 Maxim Kirichenko <kirichenko.maxim@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Common\Helpers;

final class EmailMaskHelper
{
    private const DEFAULT_MASK = '***';

    /**
     * Однозначно определяет, является ли вход именно email-значением.
     */
    public static function isEmailValue(string $value): bool
    {
        if ($value === '' || $value !== trim($value)) {
            return false;
        }

        if (substr_count($value, '@') !== 1) {
            return false;
        }

        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }

        [$localPart, $domainPart] = explode('@', $value, 2);

        if ($localPart === '' || $domainPart === '' || strpos($domainPart, '.') === false) {
            return false;
        }

        if (str_contains($localPart, '..') || str_contains($domainPart, '..')) {
            return false;
        }

        foreach (explode('.', $domainPart) as $label) {
            if ($label === '' || str_starts_with($label, '-') || str_ends_with($label, '-')) {
                return false;
            }
        }

        return true;
    }

    /**
     * Маскирует email для UI/логов.
     *
     * Примеры:
     * - super.user@mail.com => su***r@mail.com
     * - ab@mail.com         => a***b@mail.com
     * - a@mail.com          => a***@mail.com
     *
     * @param string $email
     * @param int $keepStart Сколько символов оставить в начале local-part
     * @param int $keepEnd   Сколько символов оставить в конце local-part
     * @param string $mask   Чем маскировать середину
     */
    public static function maskEmail(string $email, int $keepStart = 2, int $keepEnd = 1, string $mask = self::DEFAULT_MASK): string
    {
        $email = trim($email);

        if (! self::isEmailValue($email)) {
            // не email — вернём как есть, чтобы не ломать UI
            return $email;
        }

        $atPos = strpos($email, '@');

        $localPart  = substr($email, 0, $atPos);
        $domainPart = substr($email, $atPos + 1);

        if ($localPart === '' || $domainPart === '') {
            return $email;
        }

        // Нормализуем параметры
        $keepStart = max(0, $keepStart);
        $keepEnd   = max(0, $keepEnd);

        $localLen = strlen($localPart);

        // Если нечего “серединить” — всё равно добавим mask между частями (или после start)
        $start = ($keepStart > 0) ? substr($localPart, 0, min($keepStart, $localLen)) : '';
        $end   = ($keepEnd > 0) ? substr($localPart, max(0, $localLen - $keepEnd), $keepEnd) : '';

        // Если keepStart + keepEnd >= длины local-part — уменьшим end, чтобы не дублировать символы
        if (($keepStart + $keepEnd) >= $localLen) {
            $end = '';
        }

        return $start . $mask . $end . '@' . $domainPart;
    }
}
