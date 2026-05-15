<?php
declare(strict_types=1);

namespace Application\Core\Debug;

interface DebuggerInterface
{
    public function dumpToFile(mixed $value, ?string $file = null, bool $append = true): void;
    public function dump(mixed $value, bool $die = false): void;
    public function log(mixed $value, string $channel = 'debug'): void;
}