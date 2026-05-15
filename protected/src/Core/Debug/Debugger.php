<?php
declare (strict_types = 1);

namespace Application\Core\Debug;

use Scaleum\Stdlib\Base\Hydrator;
use Scaleum\Stdlib\SAPI\Explorer;
use Scaleum\Stdlib\SAPI\SapiMode;

final class Debugger extends Hydrator implements DebuggerInterface {
    protected ?string $debugDir = null;

    private function getDir(): string {
        if ($this->debugDir === null) {
            $this->debugDir = sys_get_temp_dir() . '/debug';
        }
        return $this->debugDir;
    }

    public function dumpToFile(mixed $value, ?string $file = null, bool $append = true): void {
        $dir = $this->getDir();
        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if (! is_writable($dir)) {
            return;
        }

        if (is_array($value)) {
            $value = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } elseif (is_object($value)) {
            $value = json_encode($this->normalizeObjectForJson($value), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        $id = md5(random_int(1000, 9999) . "_" . microtime(true));
        $file ??= "debug_{$id}.dump";
        $flags = $append ? FILE_APPEND : 0;

        file_put_contents("{$dir}/{$file}", $value, $flags);
    }

    private function normalizeObjectForJson(object $value): mixed {
        if ($value instanceof \JsonSerializable) {
            return $value->jsonSerialize();
        }

        if (method_exists($value, 'toArray')) {
            try {
                return $value->toArray();
            } catch (\Throwable) {
                return $value;
            }
        }

        return $value;
    }

    public function dump(mixed $value, bool $die = false): void {
        $isCli = Explorer::getTypeFamily() !== SapiMode::CONSOLE;
        if ($isCli) {
            fwrite(STDOUT, print_r($value, true) . PHP_EOL);
        } else {
            echo '<pre style="background:#111;color:#0f0;padding:10px;white-space:pre-wrap">';
            echo htmlspecialchars(print_r($value, true), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            echo '</pre>';
        }

        if ($die) {
            exit(0);
        }
    }

    public function log(mixed $value, string $channel = 'debug'): void {
        $line = '[' . date('Y-m-d H:i:s') . "][$channel] " . trim(print_r($value, true)) . PHP_EOL;
        error_log($line);
    }
}
