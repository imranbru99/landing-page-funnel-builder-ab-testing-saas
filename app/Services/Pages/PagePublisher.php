<?php

namespace App\Services\Pages;

use App\Models\Page;
use App\Models\PageVersion;
use App\Services\Analytics\AnalyticsTracker;
use Illuminate\Support\Str;

class PagePublisher
{
    public function __construct(
        protected AnalyticsTracker $analytics
    ) {}

    public function saveVersion(Page $page, ?string $label = null, ?int $userId = null): PageVersion
    {
        $next = (int) $page->versions()->max('version') + 1;

        $version = $page->versions()->create([
            'organization_id' => $page->organization_id,
            'version' => $next,
            'label' => $label ?? "Version {$next}",
            'sections' => $page->sections,
            'styles' => $page->styles,
            'seo' => $page->seo,
            'created_by' => $userId,
        ]);

        $page->update(['version' => $next]);

        return $version;
    }

    public function publish(Page $page, ?string $path = null, ?string $subdomain = null, ?int $userId = null): Page
    {
        $this->saveVersion($page, 'Published snapshot', $userId);

        $publishPath = $path ?: ($page->publish_path ?: Str::slug($page->slug));
        $sub = $subdomain ?: ($page->subdomain ?: Str::slug($page->organization->slug.'-'.$page->slug));

        $page->update([
            'status' => 'published',
            'publish_path' => $publishPath,
            'subdomain' => $sub,
            'published_at' => now(),
        ]);

        $this->analytics->track([
            'organization_id' => $page->organization_id,
            'project_id' => $page->project_id,
            'page_id' => $page->id,
            'event_name' => 'page_published',
            'properties' => [
                'path' => $publishPath,
                'subdomain' => $sub,
            ],
        ]);

        return $page->fresh();
    }

    public function unpublish(Page $page): Page
    {
        $page->update([
            'status' => 'draft',
            'published_at' => null,
        ]);

        return $page->fresh();
    }

    public function restoreVersion(Page $page, PageVersion $version): Page
    {
        if ($version->page_id !== $page->id) {
            throw new \InvalidArgumentException('Version does not belong to page.');
        }

        $page->update([
            'sections' => $version->sections,
            'styles' => $version->styles,
            'seo' => $version->seo,
            'version' => $version->version,
            'status' => 'draft',
        ]);

        return $page->fresh();
    }

    public function findPublishedBySubdomain(string $subdomain): ?Page
    {
        return Page::query()
            ->where('subdomain', $subdomain)
            ->where('status', 'published')
            ->first();
    }

    public function findPublishedByPath(string $path): ?Page
    {
        return Page::query()
            ->where('publish_path', $path)
            ->where('status', 'published')
            ->first();
    }
}
