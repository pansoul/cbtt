<?php

namespace App\Menu;

use App\Traits\Makeable;
use Iterator;

class Menu implements Iterator
{
    use Makeable;

    protected int $position = 0;

    protected array $items = [];

    public function __construct(array $items = [])
    {
        $this->prepare($items);
    }

    protected function prepare(array $items = []): void
    {
        $this->items = [];

        foreach ($items as $item) {
            if ($item instanceof MenuItem) {
                $this->add($item);
            } else {
                $submenu = new self();

                if (isset($item['submenu']) && is_array($item['submenu'])) {
                    $submenu->prepare($item['submenu']);
                }

                $this->add(new MenuItem(
                    $item['title'] ?? '',
                    $item['url'] ?? '#',
                    $submenu
                ));
            }
        }

        $this->reposition();
    }

    protected function reposition(): void
    {
        foreach ($this->items as $position => $item) {
            $item->position($position);
        }
    }

    public function current(): mixed
    {
        return $this->items[$this->position];
    }

    public function next(): void
    {
        $this->position++;
    }

    public function key(): mixed
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->items[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function add(MenuItem $item, bool $force = false): void
    {
        if (! $force && $item->parent() === $this) {
            return;
        }

        $item->parent($this);
        $this->items[] = $item;
    }

    public function remove(int $position): void
    {
        if (! isset($this->items[$position])) {
            return;
        }

        unset($this->items[$position]);

        $this->items = array_values($this->items);

        $this->reposition();
        $this->rewind();
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function asArray(): array
    {
        return array_map(function ($item) {
            return $item->asArray();
        }, $this->items);
    }
}
