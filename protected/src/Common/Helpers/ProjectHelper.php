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

 namespace Application\Common\Helpers;

use Scaleum\Stdlib\Helpers\PathHelper;

 /**
  * ProjectHelper
  *
  * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
  */
 class ProjectHelper
 {
    public static function getPublicDir(): string
    {
        return PathHelper::join(dirname(__DIR__,4),"public");
        // return realpath(__DIR__ . '/../../../../public');
    }

    public static function getProjectRoot(): string
    {
        return dirname(__DIR__, 2);
    }
 }
 /** End of ProjectHelper **/