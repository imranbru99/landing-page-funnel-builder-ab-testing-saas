<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Experiment;
use App\Services\Analytics\AnalyticsTracker;
use App\Services\Experiments\ExperimentAllocator;
use App\Services\Pages\PagePublisher;
use Illuminate\Http\Request;

class PublishedPageController extends Controller
{
    public function __construct(
        protected PagePublisher $publisher,
        protected ExperimentAllocator $allocator,
        protected AnalyticsTracker $analytics
    ) {}

    public function subdomain(Request $request, string $subdomain)
    {
        $page = $this->publisher->findPublishedBySubdomain($subdomain);
        abort_unless($page, 404);

        return $this->render($request, $page);
    }

    public function path(Request $request, string $path)
    {
        $page = $this->publisher->findPublishedByPath($path);
        abort_unless($page, 404);

        return $this->render($request, $page);
    }

    protected function render(Request $request, $page)
    {
        $visitorKey = $this->allocator->visitorKey();
        $variant = null;
        $experiment = Experiment::query()
            ->where('page_id', $page->id)
            ->where('status', 'running')
            ->with('variants')
            ->first();

        if ($experiment) {
            $variant = $this->allocator->assign($experiment, $visitorKey);
            $this->analytics->trackExperimentEvent($experiment, $variant->id, $visitorKey, 'view');
        }

        $this->analytics->track([
            'organization_id' => $page->organization_id,
            'project_id' => $page->project_id,
            'page_id' => $page->id,
            'visitor_key' => $visitorKey,
            'event_name' => 'page_view',
            'url' => $request->fullUrl(),
            'referrer' => (string) $request->headers->get('referer'),
            'utm_source' => $request->query('utm_source'),
            'utm_medium' => $request->query('utm_medium'),
            'utm_campaign' => $request->query('utm_campaign'),
        ]);

        return response()
            ->view('published.page', [
                'page' => $page,
                'sections' => $page->sections,
                'experiment' => $experiment,
                'variant' => $variant,
                'visitorKey' => $visitorKey,
            ])
            ->cookie('lf_visitor', $visitorKey, 60 * 24 * 365);
    }
}
