<?php
declare(strict_types=1);

use Application\Core\Debug\DebuggerInterface;
use Scaleum\Services\ServiceLocator;

if (!function_exists('__get_debugger__')) {
    /**
     * Retrieves the debugger instance.
     *
     * This function returns an instance of the DebuggerInterface if available.
     * If no debugger is set up, it returns null.
     *
     * @return DebuggerInterface|null The debugger instance or null if not available.
     */
    function __get_debugger__(): ?DebuggerInterface {
        $result = ServiceLocator::get('debugger');
        if ($result instanceof DebuggerInterface) {
            return $result;
        }
        return null;
    }
}

if (!function_exists('dump')) {
    /**
     * Outputs debugging information about a given value.
     *
     * This function prints the provided value, primarily for debugging purposes.
     * Optionally, it can halt the script execution after outputting the debug information,
     * depending on the value of the second parameter.
     *
     * @param mixed $value The variable to output for debugging.
     * @param bool  $die   If set to true, halts script execution after output.
     *
     * @return void
     */
    function dump(mixed $value, bool $die = false): void {
        $debugger = __get_debugger__();
        if ($debugger) {
            $debugger->dump($value, $die);
        }
    }
}

if (!function_exists('dd')) {
    /**
     * Dumps the provided variable and terminates the execution.
     *
     * This function is primarily used for debugging purposes. It outputs
     * detailed information about the variable passed to it, which can include
     * arrays, objects, or any other data type, and then stops any further script
     * execution.
     *
     * @param mixed $value The variable to be dumped for debugging.
     */
    function dd(mixed $value): void {
        dump($value, true);
    }
}

if (!function_exists('dlog')) {
    /**
     * Logs debugging information.
     *
     * This function logs the provided value to the specified channel. If no channel is specified, it defaults to "debug".
     *
     * @param mixed  $value   The value to be logged.
     * @param string $channel The logging channel. Defaults to 'debug'.
     *
     * @return void
     */
    function dlog(mixed $value, string $channel = 'debug'): void {
        $debugger = __get_debugger__();
        if ($debugger) {
            $debugger->log($value, $channel);
        }
    }
}

if (!function_exists('dfile')) {
    /**
     * Handles file output operations.
     *
     * This function processes the given value and writes it to a specified file.
     *
     * @param mixed       $value  The data to be written or logged.
     * @param string|null $file   Optional. The file path to which the value should be written. If null, a default behavior is applied.
     * @param bool        $append Optional. If true, appends the value to the file; if false, overwrites the file. Default is true.
     *
     * @return void
     */
    function dfile(mixed $value, ?string $file = null, bool $append = true): void {
        $debugger = __get_debugger__();
        if ($debugger) {
            $debugger->dumpToFile($value, $file, $append);
        }
    }
}