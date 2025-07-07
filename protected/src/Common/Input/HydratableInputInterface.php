<?php
declare(strict_types=1);
/**
 * This file is part of Scaleum Framework.
 *
 * (C) 2009-2025 Maxim Kirichenko <kirichenko.maxim@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Application\Common\Input;


/**
 * HydratableInputInterface
 *
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 */
interface HydratableInputInterface
{
    /**
     * Возвращает список обязательных полей.
     * @return string[]
     */
    public static function getRequiredFields(): array;

    /**
     * Возвращает маппинг: свойство => имя в данных.
     * @return array<string, string>
     */
    public static function getFieldMap(): array;
}
/** End of HydratableInputInterface **/