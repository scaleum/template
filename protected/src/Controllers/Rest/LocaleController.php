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
namespace Application\Controllers\Rest;

use Application\Base\RestfulController;
use Application\Common\i18n\LocaleManagerTrait;

/**
 * LocaleController
 *
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 */
class LocaleController extends RestfulController {
    use LocaleManagerTrait;
    public function getLocales() {
        $localeManager = $this->getLocaleManager();
        $locales       = $localeManager->getLocales();
        if ($locales) {
            return $this->getResponse(['locales' => array_map(fn($locale) => $locale->toArray(), $locales)]);
        } else {
            return $this->getErrorResponse(
                404,
                'No locales found',
                ['error' => 'No locales found']
            );
        }
    }

    public function getLocale() {
        $localeManager = $this->getLocaleManager();
        $locale        = $localeManager->getLocale();
        if ($locale) {
            return $this->getResponse($locale->toArray());
        } else {
            return $this->getErrorResponse(
                404,
                'Locale not found',
                ['error' => 'Locale not found']
            );
        }
    }
    public function postLocale() {
        $locale = $this->getRequest()->getInputParam('iso', 'xx_XX');
        try {
            $this->getLocaleManager()->setLocale($locale);
            return $this->getResponse(['message' => 'Locale changed successfully']);
        } catch (\Exception $e) {
            return $this->getErrorResponse(
                400,
                'Invalid locale',
                ['error' => $e->getMessage()]
            );
        }
    }
}
/** End of LocaleController **/