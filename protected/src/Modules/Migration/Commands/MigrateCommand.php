<?php
declare (strict_types = 1);
/**
 * This file is part of Scaleum Application.
 *
 * (C) 2009-2025 Maxim Kirichenko <kirichenko.maxim@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Modules\Migration\Commands;

use Application\Common\Accessors\ConfigTrait;
use Application\Common\Accessors\DatabaseTrait;
use Application\Modules\Migration\Contracts\MigrationInterface;
use Scaleum\Console\CommandAbstract;
use Scaleum\Console\Contracts\CommandInterface;
use Scaleum\Console\Contracts\ConsoleRequestInterface;
use Scaleum\Console\Contracts\ConsoleResponseInterface;
use Scaleum\Console\Response;
use Scaleum\Core\Contracts\KernelInterface;
use Scaleum\Logger\LoggerChannelTrait;
use Scaleum\Stdlib\Exceptions\ERuntimeError;
use Scaleum\Stdlib\Helpers\FileHelper;
use Scaleum\Stdlib\Helpers\PathHelper;
use Scaleum\Storages\PDO\Database;

/**
 * MigrateCommand
 *
 * @version 1.2.0
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 */
class MigrateCommand extends CommandAbstract{
    use LoggerChannelTrait;
    use ConfigTrait;
    use DatabaseTrait {DatabaseTrait::getDatabase as fetchDb;}

    protected const DIRECT_UP          = 'up';
    protected const DIRECT_DOWN        = 'down';
    protected ?KernelInterface $kernel = null;

    public function __construct(?KernelInterface $kernel = null) {
        if ($kernel !== null) {
            $this->setKernel($kernel);
        }
    }
    public function getLoggerChannel(): string {
        return 'kernel';
    }

    public function execute(ConsoleRequestInterface $request): ConsoleResponseInterface {
        // Main config
        $this->getConfig()->fromFile($this->getKernel()->getConfigDir() . '/migrations.php', 'migrations');

        // Create migrations table if it doesn't exist
        $schema = $this->getDatabase()->getSchemaBuilder();
        $schema
            ->addColumn([
                $schema->columnPrimaryKey(11)->setColumn('migration_id'),
                $schema->columnString(256)->setColumn('migration_key')->setNotNull(),
                $schema->columnString(24)->setColumn('applied_at')->setNotNull(),
            ])
            ->createTable($this->getConfig()->get('migrations.table'), true);

        // Load options and arguments
        $this->getOptions()
            ->setOptsLong(["create", "name::", "up", "down", "size::","source::"])
            ->setArgs($request->getRawArguments())
            ->parse();

        $response = new Response();
        try {
            $this->getConfig()->set('migrations.source', $this->getOptions()->get('source'));

            if ($this->getOptions()->get('create')) {
                $this->createMigration($this->getOptions()->get('name'));
            } elseif ($this->getOptions()->get('up')) {
                $this->run(self::DIRECT_UP, (int) $this->getOptions()->get('size', 0));
            } elseif ($this->getOptions()->get('down')) {
                $this->run(self::DIRECT_DOWN, (int) $this->getOptions()->get('size', 0));
            } else {
                // Show help message
                $this->printLine("Migration command usage:");
                $this->printLine("");
                $this->printLine("  php index.php migrate [--create] [--name=NAME] [--up] [--down] [--size=N] [--source=KEY] [--help]");
                $this->printLine("");
                $this->printLine("Options:");
                $this->printLine("  --create        Create a new migration (flag, no value)");
                $this->printLine("  --name=NAME     Name of the migration being created (optional)");
                $this->printLine("  --up            Apply migrations (flag, no value)");
                $this->printLine("  --down          Revert migrations (flag, no value)");
                $this->printLine("  --size=N        Number of migrations to apply or revert (optional)");
                $this->printLine("  --source=KEY    Key in migration folders configuration (if multiple sources are used) (optional)");
                $this->printLine("  --help          Show this help message");

                $response->setStatusCode(ConsoleResponseInterface::STATUS_NOT_FOUND);
            }
        } catch (\Throwable $e) {
            $response->setStatusCode(ConsoleResponseInterface::STATUS_INVALID_PARAMS);
            $this->printLine("Error: " . $e->getMessage());
        }

        $this->printLine("Done.");
        return $response;
    }

    protected function run(?string $direct = null, ?int $size = 0) {
        // normalize
        if ($direct === NULL || ! in_array($direct = strtolower($direct), [self::DIRECT_UP, self::DIRECT_DOWN])) {
            $direct = self::DIRECT_UP;
        }

        $items      = $this->getMigrations($direct, $size);
        $itemsCount = count($items);
        $this->printLine(sprintf("Found %d migrations...", $itemsCount));
        if ($itemsCount > 0) {
            foreach ($items as $mgrKey => $mgr) {
                if (file_exists($filename = $mgr['filename'])) {
                    $migration = require $filename;
                    if (! is_object($migration)) {
                        throw new ERuntimeError("Migration '{$filename}' does not return object.");
                    }

                    if (! ($migration instanceof MigrationInterface)) {
                        throw new ERuntimeError("Migration '{$filename}' does not return MigrationInterface instance.");
                    }

                    if ($direct === self::DIRECT_UP) {
                        $migration->up();
                        ($this->getDatabase()->getQueryBuilder())
                            ->insert($this->getConfig()->get('migrations.table'), [
                                'migration_key' => $mgrKey,
                                'applied_at'    => date('Y-m-d H:i:s'),
                            ]);
                    } else {
                        ($this->getDatabase()->getQueryBuilder())
                            ->delete($this->getConfig()->get('migrations.table'), [
                                'migration_key' => $mgrKey,
                            ]);
                        $migration->down();
                    }
                    $this->printLine(sprintf("Migration %s has been successfully applied(%s)", $mgrKey, $direct));
                }
            }
        }
    }
    protected function getMigrations(?string $direct = null, ?int $size = 0): array {
        $result = [];
        $isDown = $direct === self::DIRECT_DOWN;
        $size   = ($size === 0 && $isDown) ? 1 : $size;

        $folders = $this->getFolders();
        $source  = $this->getConfig()->get('migrations.source');
        if ($source) {
            // filter folders by key using the source string
            $folders = array_filter($folders, function ($key) use ($source) {
                return strpos($key, $source) !== false;
            }, ARRAY_FILTER_USE_KEY);
        }

        foreach ($folders as $folder) {
            if (is_dir($folder)) {
                $migrationDir = $folder;
                foreach (glob($migrationDir . '/*.php') as $file) {
                    $info = pathinfo($file);
                    $key  = $info['filename']; // имя файла без расширения

                    $result[$key] = [
                        'filename' => $file,
                        'direct'   => self::DIRECT_UP,
                    ];
                }
            }
        }

        ksort($result, SORT_STRING);

        // Отмечаем применённые миграции как готовые к откату
        $query = $this->getDatabase()->getQueryBuilder();
        $rows  = $query->select()->from($this->getConfig()->get('migrations.table'))->orderBy('applied_at')->rows();

        if (is_array($rows) && count($rows) > 0) {
            foreach ($rows as $row) {
                if (isset($result[$row['migration_key']])) {
                    $result[$row['migration_key']]['direct'] = self::DIRECT_DOWN;
                }
            }
        }

        // Фильтрация по направлению
        if ($direct !== null) {
            $result = array_filter($result, fn($v) => $v['direct'] === $direct);

            if ($isDown) {
                $result = array_reverse($result, true);
            }
        }

        // Ограничение количества
        return ($size > 0) ? array_slice($result, 0, $size, true) : $result;
    }

    protected function createMigration(?string $name = null): void {
        $template = <<<PHP
        <?php

        declare(strict_types=1);

        use Application\Modules\Migration\Contracts\MigrationInterface;
        use Application\Modules\Migration\Base\MigrationAbstract;

        return new class extends MigrationAbstract implements MigrationInterface {
            public function up(): void
            {
                // TODO
            }

            public function down(): void
            {
                // TODO
            }
        };
        PHP;

        if ($name !== null) {
            $filtered = strtolower(trim($name));
            $filtered = preg_replace('/\s+/', '_', $filtered);       // пробелы → _
            $filtered = preg_replace('/[^a-z0-9_]/', '', $filtered); // убрать лишнее
            $filtered = substr($filtered, 0, 200);

            if (! empty($filtered) && $filtered !== $name) {
                $name = $filtered;
            }
        }

        $name = uniqid(date('YmdHis_')) . ($name ? "_$name" : '');

        $path     = PathHelper::join($this->getFolder(), $name);
        $filename = FileHelper::prepFilename($path, false);

        if (! $filename || ! FileHelper::writeFile($filename, $template)) {
            throw new ERuntimeError("Failed to create migration file: $filename");
        }

        $this->printLine(sprintf("Migration file '%s' was successfully created", PathHelper::overlapPath($filename, $this->getKernel()->getApplicationDir())));
    }

    protected function getFolders(): array {
        $result = [];
        $folder = $this->getConfig()->get('migrations.folder', __DIR__ . '/../migrations');

        if (! is_array($folder)) {
            $folder = ['default' => $folder];
        }

        if (empty($folder)) {
            throw new ERuntimeError("No migration folders found.");
        }

        foreach ($folder as $key => $value) {
            if (is_dir($value)) {
                $result[$key] = $value;
            } else {
                $this->printLine("Migration folder '$value' does not exist or is not a directory.", true);
            }
        }

        return $result;
    }

    protected function getFolder():string{
        $folders = $this->getFolders();
        if (empty($folders)) {
            throw new ERuntimeError("No migration folders found.");
        } 
    
        if(($source  = $this->getConfig()->get('migrations.source')) && isset($folders[(string)$source])) {
            // If source is set and exists in folders, return that folder
            return $folders[$source];
        }

        // Return the first folder in the array
        return reset($folders);
    }

    protected function createMigrationKey(?string $name = null) {
        $name = strtr($name, ['-' => '_', ' ' => '_', '\\' => '_', '/' => '_']);
        $name = substr($name, 0, 220);

        return uniqid(date('YmdHis_')) . ($name ? "_$name" : '_migration');
    }

    public function getKernel(): KernelInterface {
        if ($this->kernel === null || ! $this->kernel instanceof KernelInterface) {
            throw new ERuntimeError('Kernel is not set');
        }
        return $this->kernel;
    }

    public function setKernel(KernelInterface $kernel): void {
        $this->kernel = $kernel;
    }

    public function getDatabase(): Database {
        return $this->fetchDb($this->getConfig()->get('migrations.database', 'db'));
    }

    public function printLine(string $message, bool $isError = false): void {
        if ($this->getConfig()->get('migrations.debug', false)) {
            $this->getLogger()->debug($message);
        }
        parent::printLine($message, $isError);
    }
}
/** End of MigrateCommand **/