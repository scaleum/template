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

namespace Application\Base;

use Application\Common\Traits\ConfigTrait;
use Scaleum\Http\Renderers\TemplateRenderer;

/**
 * PageController
 *
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 */
abstract class PageController extends ControllerAbstract {
    use ConfigTrait;
    protected TemplateRenderer $renderer;

    public function init(): void {
        $this->getConfig()->fromFile($this->getKernel()->getConfigDir() . '/renderer.php', 'renderer');
        $this->setRenderer(new TemplateRenderer((array) $this->getConfig()->get('renderer')));
    }

    public function getRenderer(): TemplateRenderer {
        return $this->renderer;
    }

    public function setRenderer(TemplateRenderer $renderer): static
    {
        $this->renderer = $renderer;
        return $this;
    }

    public function render(string $view, array $data = [], $partial = false): string {
        return $this->renderer->render($view, $data, $partial);
    }

    public function renderStr(string $str, array $data = []): string {
        return $this->render($str, $data, true);
    }
}
/** End of PageController **/