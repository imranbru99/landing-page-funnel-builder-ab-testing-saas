<?php

namespace App\Livewire\PageBuilder;

use App\Models\Page;
use App\Services\Pages\PagePublisher;
use Illuminate\Support\Str;
use Livewire\Component;

class PageEditor extends Component
{
    public Page $page;

    public array $sections = [];

    public string $previewDevice = 'desktop';

    public string $message = '';

    public function mount(Page $page): void
    {
        $this->page = $page;
        $this->sections = $page->sections ?: [
            [
                'id' => (string) Str::uuid(),
                'type' => 'hero',
                'props' => [
                    'headline' => $page->title,
                    'subheadline' => 'Edit this section to launch faster.',
                    'cta' => 'Get Started',
                    'cta_url' => '#',
                ],
            ],
        ];
    }

    public function addSection(string $type): void
    {
        $defaults = match ($type) {
            'hero' => ['headline' => 'New hero', 'subheadline' => 'Supporting copy', 'cta' => 'Learn more', 'cta_url' => '#'],
            'features' => ['title' => 'Features', 'items' => [['title' => 'Feature', 'body' => 'Description']]],
            'cta' => ['headline' => 'Ready to convert?', 'button' => 'Start now', 'url' => '#'],
            'testimonials' => ['title' => 'What customers say', 'quotes' => [['name' => 'Alex', 'quote' => 'Amazing results.']]],
            'form' => ['title' => 'Get the guide', 'form_slug' => 'lead-capture'],
            'faq' => ['title' => 'FAQ', 'items' => [['q' => 'Question?', 'a' => 'Answer.']]],
            'html' => ['html' => '<p>Custom content</p>'],
            default => ['title' => 'Section'],
        };

        $this->sections[] = [
            'id' => (string) Str::uuid(),
            'type' => $type,
            'props' => $defaults,
        ];
    }

    public function removeSection(int $index): void
    {
        unset($this->sections[$index]);
        $this->sections = array_values($this->sections);
    }

    public function moveUp(int $index): void
    {
        if ($index <= 0) {
            return;
        }

        [$this->sections[$index - 1], $this->sections[$index]] = [$this->sections[$index], $this->sections[$index - 1]];
        $this->sections = array_values($this->sections);
    }

    public function moveDown(int $index): void
    {
        if ($index >= count($this->sections) - 1) {
            return;
        }

        [$this->sections[$index + 1], $this->sections[$index]] = [$this->sections[$index], $this->sections[$index + 1]];
        $this->sections = array_values($this->sections);
    }

    public function saveDraft(): void
    {
        $this->page->update([
            'sections' => $this->sections,
            'status' => $this->page->status === 'published' ? 'published' : 'draft',
        ]);

        app(PagePublisher::class)->saveVersion($this->page, 'Autosave', auth()->id());
        $this->message = 'Draft saved.';
    }

    public function publish(): void
    {
        $this->page->update(['sections' => $this->sections]);
        app(PagePublisher::class)->publish($this->page, null, null, auth()->id());
        $this->page->refresh();
        $this->message = 'Published at '.$this->page->publicUrl();
    }

    public function render()
    {
        return view('livewire.page-builder.page-editor')
            ->layout('layouts.app', ['title' => 'Page Editor']);
    }
}
