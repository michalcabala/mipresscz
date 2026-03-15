<?php

namespace MiPressCz\Core\Filament\Resources\Terms;

use Filament\Resources\ResourceConfiguration;

class TermResourceConfiguration extends ResourceConfiguration
{
    protected ?string $taxonomyHandle = null;

    protected ?string $navigationLabel = null;

    protected ?string $navigationParentItem = null;

    protected ?string $navigationIcon = null;

    protected ?int $navigationSort = null;

    public function taxonomyHandle(?string $handle): static
    {
        $this->taxonomyHandle = $handle;

        return $this;
    }

    public function getTaxonomyHandle(): ?string
    {
        return $this->taxonomyHandle;
    }

    public function navigationLabel(?string $label): static
    {
        $this->navigationLabel = $label;

        return $this;
    }

    public function getNavigationLabel(): ?string
    {
        return $this->navigationLabel;
    }

    public function navigationParentItem(?string $label): static
    {
        $this->navigationParentItem = $label;

        return $this;
    }

    public function getNavigationParentItem(): ?string
    {
        return $this->navigationParentItem;
    }

    public function navigationIcon(?string $icon): static
    {
        $this->navigationIcon = $icon;

        return $this;
    }

    public function getNavigationIcon(): ?string
    {
        return $this->navigationIcon;
    }

    public function navigationSort(?int $sort): static
    {
        $this->navigationSort = $sort;

        return $this;
    }

    public function getNavigationSort(): ?int
    {
        return $this->navigationSort;
    }
}
