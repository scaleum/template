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

use Application\Common\Helpers\ProjectHelper;
use ErrorException;
use Scaleum\Stdlib\Exceptions\EBasicException;
use Scaleum\Stdlib\Exceptions\ExceptionOutputHttp;
use Scaleum\Stdlib\Helpers\FileHelper;
use Scaleum\Stdlib\Helpers\HttpHelper;
use Scaleum\Stdlib\Helpers\PathHelper;

/**
 * ErrorInterceptor
 *
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 */
class ErrorInterceptor extends ExceptionOutputHttp {
    public function render(\Throwable $exception): void {

        $this->statusCode = HttpHelper::isStatusCode(
            $code = $exception instanceof ErrorException ? ($exception instanceof EBasicException ? $exception->getCode() : $exception->getSeverity()) : $exception->getCode()
        ) ? $code : 500;

        $filename = FileHelper::prepFilename(PathHelper::join(ProjectHelper::getPublicDir(), "error", "{$this->statusCode}.html"));
        if (! file_exists($filename) || HttpHelper::isAjaxRequest()) {
            parent::render($exception);
            return;
        }

        HttpHelper::setStatusHeader($this->statusCode);
        readfile($filename);
    }
}
/** End of ErrorInterceptor **/