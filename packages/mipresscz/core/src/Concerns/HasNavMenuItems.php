<?php

namespace MiPressCz\Core\Concerns;

/**
 * Enables an Eloquent model to be used as a source for navigation menu items.
 *
 * Implement the four methods to control how the model appears in the menu builder
 * and how its items render in the frontend navigation.
 */
trait HasNavMenuItems
{
    /**
     * Returns the display label for this record in the menu builder and frontend.
     */
    public function getMenuLabel(): string
    {
        return (string) ($this->title ?? $this->name ?? $this->getKey());
    }

    /**
     * Returns the URL this menu item links to.
     */
    public function getMenuUrl(): ?string
    {
        return $this->uri ? url($this->uri) : null;
    }

    /**
     * Returns the link target attribute (e.g. '_self', '_blank').
     */
    public function getMenuTarget(): string
    {
        return '_self';
    }

    /**
     * Returns an optional icon string to display alongside this item.
     */
    public function getMenuIcon(): ?string
    {
        return null;
    }
}
