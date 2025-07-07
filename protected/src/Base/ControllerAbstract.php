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

use Application\Common\Traits\EventsTrait;
use Scaleum\Core\Contracts\KernelInterface;
use Scaleum\Http\InboundRequest;
use Scaleum\Stdlib\Exceptions\ERuntimeError;

/**
 * ControllerAbstract
 *
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 */
abstract class ControllerAbstract {
    use EventsTrait;
    protected ?InboundRequest $request = null;
    protected ?KernelInterface $kernel = null;

    public function __construct(?KernelInterface $kernel = null) {
        if ($kernel !== null) {
            $this->setKernel($kernel);
        }
        $this->init();
    }

    protected function init(): void {
        //...
    }

    public function getKernel(): KernelInterface {
        if ($this->kernel === null  || ! $this->kernel instanceof KernelInterface) {
            throw new ERuntimeError('Kernel is not set');
        }
        return $this->kernel;
    }

    public function setKernel(KernelInterface $kernel): void {
        $this->kernel = $kernel;
    }

    public function getRequest(): InboundRequest {
        if ($this->request === null) {
            $this->request = InboundRequest::fromGlobals();
        }
        return $this->request;
    }

    public function setRequest(InboundRequest $request): static {
        $this->request = $request;
        return $this;
    }
}
/** End of ControllerAbstract **/