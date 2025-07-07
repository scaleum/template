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

namespace Application\Services;

use Scaleum\Session\SessionInterface;

/**
 * FlashSession
 *
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 */
class FlashSession {
    protected SessionInterface $session;
    protected string $prefix = 'flash:';

    public function __construct(SessionInterface $session) {
        $this->session = $session;
    }

    public function set(string $key, mixed $value): void {
        $this->session->set($this->prefix . $key, $value);
    }

    public function get(string $key, mixed $default = null): mixed {
        $flashKey = $this->prefix . $key;
        $value    = $this->session->get($flashKey, $default);
        
        $this->session->set($flashKey, null); // автоочистка
        return $value;
    }
}
/** End of FlashSession **/
