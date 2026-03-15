<?php

namespace MiPressCz\Core\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use MiPressCz\Core\Models\Setting;

class TemplateManager
{
    /** @var Collection<int, array{name: string, slug: string, version: string, author: string, description: string, screenshot: string, screenshot_exists: bool, path: string}>|null */
    private ?Collection $availableTemplates = null;

    private ?string $activeTemplate = null;

    /**
     * Scan the templates directory and return all available templates.
     *
     * @return Collection<int, array{name: string, slug: string, version: string, author: string, description: string, screenshot: string, screenshot_exists: bool, path: string}>
     */
    public function getAvailable(): Collection
    {
        return $this->availableTemplates ??= (function (): Collection {
            $templatesPath = resource_path('views/templates');

            if (! File::isDirectory($templatesPath)) {
                return collect();
            }

            return collect(File::directories($templatesPath))
                ->map(function (string $path): ?array {
                    $jsonPath = $path.DIRECTORY_SEPARATOR.'template.json';

                    if (! File::exists($jsonPath)) {
                        return null;
                    }

                    $data = json_decode(File::get($jsonPath), true);

                    if (! is_array($data)) {
                        return null;
                    }

                    $screenshotFile = $data['screenshot'] ?? 'screenshot.png';

                    return array_merge($data, [
                        'path' => $path,
                        'screenshot_exists' => File::exists($path.DIRECTORY_SEPARATOR.$screenshotFile),
                    ]);
                })
                ->filter()
                ->values();
        })();
    }

    /**
     * Return the slug of the currently active template.
     * Falls back to 'default' if the settings table is unavailable.
     */
    public function getActive(): string
    {
        if ($this->activeTemplate !== null) {
            return $this->activeTemplate;
        }

        try {
            return $this->activeTemplate = Setting::get('active_template', 'default');
        } catch (\Throwable) {
            return $this->activeTemplate = 'default';
        }
    }

    /**
     * Set the active template by slug.
     *
     * @throws \InvalidArgumentException When the template slug does not exist.
     */
    public function setActive(string $slug): void
    {
        $available = $this->getAvailable()->pluck('slug');

        if (! $available->contains($slug)) {
            throw new \InvalidArgumentException("Template '{$slug}' does not exist.");
        }

        Setting::set('active_template', $slug);
        $this->activeTemplate = $slug;
        $this->registerViewNamespace();
    }

    /**
     * Return the absolute path to the given template's directory.
     */
    public function getPath(string $slug): string
    {
        return resource_path("views/templates/{$slug}");
    }

    /**
     * Register the 'template' Blade namespace pointing to the active template,
     * with automatic fallback to the 'default' template directory.
     */
    public function registerViewNamespace(): void
    {
        $active = $this->getActive();
        $paths = [$this->getPath($active)];

        if ($active !== 'default') {
            $defaultPath = $this->getPath('default');

            if (File::isDirectory($defaultPath)) {
                $paths[] = $defaultPath;
            }
        }

        $validPaths = array_values(array_filter($paths, fn (string $p) => File::isDirectory($p)));

        if (! empty($validPaths)) {
            app('view')->addNamespace('template', $validPaths);
        }
    }
}
