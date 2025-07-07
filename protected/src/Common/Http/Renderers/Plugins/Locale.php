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
namespace Application\Common\Http\Renderers\Plugins;

use Scaleum\Http\Renderers\Plugins\RendererPluginInterface;
use Scaleum\Http\Renderers\TemplateRenderer;
use Scaleum\i18n\Translator;
use Scaleum\Services\ServiceLocator;

/**
 * Locale
 *
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 */
class Locale implements RendererPluginInterface {
    protected $renderer;

    public function getName(): string {
        return 'locale';
    }

    public function register(TemplateRenderer $renderer): void {
        $this->renderer = $renderer;
    }

    public function __invoke() {
        $args   = func_get_args();
        $result = '';
        if (($instance = ServiceLocator::get('translator')) instanceof Translator) {
            if (count($args) > 1) {
                $locale = reset($args);
                $text   = end($args);
                if ($instance->getLocale() === $locale) {
                    $result = $text;
                }
            } else {
                $result = $instance->getLocale();
            }
        }

        return $result;
    }
}
/** End of Locale **/