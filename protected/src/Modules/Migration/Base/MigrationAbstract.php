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

namespace Application\Modules\Migration\Base;

use Scaleum\Services\ServiceLocator;
use Scaleum\Stdlib\Exceptions\ERuntimeError;
use Scaleum\Storages\PDO\Database;

/**
 * MigrationAbstract
 *
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 */
abstract class MigrationAbstract {
    public function getDatabase(): Database {
        $db = ServiceLocator::get('db');
        if (! $db instanceof Database) {
            throw new ERuntimeError('Database service is not set or invalid');
        }
        return $db;
    }
}
/** End of MigrationAbstract **/