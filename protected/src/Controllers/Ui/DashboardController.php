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

namespace Application\Controllers\Ui;

use Application\Base\PageController;
use Application\Common\i18n\LocaleManagerTrait;
use Application\Common\Traits\SessionTrait;
use Application\Modules\Security\Authentication\Traits\UserTrait;
use Scaleum\Http\OutboundResponse;

/**
 * Controller
 *
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 */
class DashboardController extends PageController {
    use SessionTrait;
    
    use LocaleManagerTrait;
    public function index() {
        $lang    = $this->getLocaleManager();
        $content = $this->render('index.view', ['name' => 'Guest']);
        return new OutboundResponse(body: $content, headers: []);
    }
}
/** End of Controller **/