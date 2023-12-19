<?php

namespace App\Menu;

use App\Traits\Makeable;

class MenuItem
{
    use Makeable;

    protected int $position = 0;

    protected ?Menu $parent = null;

    protected bool $active = false;

    public function __construct(
        protected string $title = '',
        protected string $url = '#',
        protected Menu $submenu = new Menu,
    ) {
        $this->active($this->detectActivity());
    }

    public function title(string $value = null)
    {
        if (is_null($value)) {
            return $this->title;
        }

        $this->title = $value;
    }

    public function url(string $value = null)
    {
        if (is_null($value)) {
            return $this->url;
        }

        $this->url = $value;
    }

    public function submenu(Menu $value = null)
    {
        if (is_null($value)) {
            return $this->submenu;
        }

        $this->submenu = $value;
    }

    public function active(bool $value = null)
    {
        if (is_null($value)) {
            return $this->active;
        }

        $this->active = $value;
    }

    public function detectActivity(): bool
    {
        $path = parse_url($this->url, PHP_URL_PATH) ?? '/';

        if ($path === '/') {
            return request()->path() === $path;
        }

        return request()->fullUrlIs($this->url.'*');
    }

    public function position(int $value = null)
    {
        if (is_null($value)) {
            return $this->position;
        }

        $this->position = $value;
    }

    public function parent(Menu $value = null)
    {
        if (is_null($value)) {
            return $this->parent;
        }

        if ($this->parent === $value) {
            return;
        }

        $force = ! is_null($this->parent);
        $this->parent = $value;
        $value->add($this, $force);
    }

    public function remove(): void
    {
        $this->parent->remove($this->position);
    }

    public function hasSubmenu(): bool
    {
        return $this->submenu->count() > 0;
    }

    public function addToSubmenu(MenuItem $item): void
    {
        $this->submenu->add($item);
    }

    public function removeFromSubmenu(int $position): void
    {
        $this->submenu->remove($position);
    }

    public function asArray(): array
    {
        return [
            'title' => $this->title,
            'url' => $this->url,
            'active' => $this->active,
            'position' => $this->position,
            'submenu' => $this->submenu->asArray(),
        ];
    }
}
