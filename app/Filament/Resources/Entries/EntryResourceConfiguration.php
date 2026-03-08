<?php

namespace App\Filament\Resources\Entries;

use Filament\Resources\ResourceConfiguration;

class EntryResourceConfiguration extends ResourceConfiguration
{
    protected ?string $collectionHandle = null;

    protected ?string $navigationLabel = null;

    protected ?string $navigationIcon = null;

    protected ?int $navigationSort = null;

    public function collectionHandle(?string $handle): static
    {
        $this->collectionHandle = $handle;

        return $this;
    }

    public function getCollectionHandle(): ?string
    {
        return $this->collectionHandle;
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
