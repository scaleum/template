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
namespace Application\Common\Input;

/**
 * EntityNode
 *
 * @author Maxim Kirichenko <kirichenko.maxim@gmail.com>
 */
class EntityNode {
    protected mixed $value;
    protected ?string $path = null;

    public function __construct(mixed $data, ?string $prefix = null) {
        $this->path  = $prefix;
        $this->value = is_array($data) ? $this->parse($data, $this->path) : $data;
    }

    protected function parse(array $haystack, ?string $prefix = null): array {
        $result = [];
        foreach ($haystack as $key => $value) {
            $path         = $prefix ? "{$prefix}[{$key}]" : (string) $key;
            $result[$key] = new static($value, $path);
        }
        return $result;
    }

    public function path(): string {
        return $this->path ?? '';
    }

    public function getChildren(): array {
        return $this->asArray() ?? [];
    }

    public function getChild(mixed $key, mixed $value = null): ?static {
        $children = $this->getChildren();

        if (! array_key_exists($key, $children) && $value !== null) {
            if (! $value instanceof self) {
                $path  = $this->path ? "{$this->path}[{$key}]" : (string) $key;
                $value = new static($value, $path);
            }
            $children[$key] = $value;
            $this->value    = $children;
        }

        return $children[$key] ?? null;
    }

    public function unwrap() : mixed {
        if (! is_array($this->value)) {
            return $this->value;
        }

        $result = [];
        foreach ($this->value as $key => $node) {
            if ($node instanceof self) {
                $result[$key] = $node->unwrap();
            } else {
                $result[$key] = $node;
            }
        }

        return $result;
    }

    public function flatten(): array {
        $result = [];

        if (! is_array($this->value)) {
            if ($this->path !== null) {
                $result[$this->path] = $this->value;
            }
            return $result;
        }

        foreach ($this->value as $node) {
            if ($node instanceof self) {
                $result += $node->flatten();
            }
        }

        return $result;
    }

    public function flattenWithPath(): array {
        $result = [];

        if (! is_array($this->value)) {
            if ($this->path !== null) {
                $result[$this->path] = $this;
            }
            return $result;
        }

        foreach ($this->value as $node) {
            if ($node instanceof self) {
                $result += $node->flattenWithPath();
            }
        }

        return $result;
    }

    public function asRaw(): mixed {
        return $this->value;
    }

    public function asInt(): ?int {
        return is_numeric($this->value) ? (int) $this->value : null;
    }

    public function asFloat(): ?float {
        return is_numeric($this->value) ? (float) $this->value : null;
    }

    public function asBool(): ?bool {
        if (is_bool($this->value)) {
            return $this->value;
        }
        if (is_scalar($this->value) || is_null($this->value)) {
            return (bool) $this->value;
        }
        return null;
    }

    public function asString(): ?string {
        if (is_scalar($this->value)) {
            return (string) $this->value;
        }
        if (is_object($this->value) && method_exists($this->value, '__toString')) {
            return (string) $this->value;
        }
        return null;
    }

    public function asArray(): ?array {
        if (is_array($this->value)) {
            return $this->value;
        }
        if (is_scalar($this->value)) {
            return [$this->value];
        }
        return null;
    }
}
/** End of EntityNode **/