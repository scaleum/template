<?php
/**
 * This file is part of Scaleum Framework.
 *
 * (C) 2009-2025 Maxim Kirichenko <kirichenko.maxim@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    'migrate'  => \Application\Modules\Migration\Commands\MigrateCommand::class,
    'pwd-hash' => \Application\Commands\PasswordHashCommand::class,
];