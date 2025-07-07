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
namespace Application\Common\i18n;

use Application\Common\i18n\LocaleManager;
use Scaleum\Services\ServiceLocator;

/**
 * LangManagerTrait
 *
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 */
trait LocaleManagerTrait
{
    public function getLocaleManager(): LocaleManager{
        $result = ServiceLocator::get('lang');
        if (! $result instanceof LocaleManager) {
            throw new \RuntimeException('Lang service is not set or invalid');
        }
        return $result;
    }
}
/** End of LangManagerTrait **/