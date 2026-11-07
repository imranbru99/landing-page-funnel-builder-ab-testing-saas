<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Experiment;
use App\Models\Page;
use App\Services\Experiments\ExperimentAllocator;
use App\Services\Experiments\StatisticalSummaryService;
use App\Services\Modules\ModuleRegistry;
use App\Services\Pages\PagePublisher;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    protected function orgId(Request $request): int
    {
        $user = $request->user();
        $orgId = $user->current_organization_id ?? $user->organizations()->value('organizations.id');
        abort_unless($orgId, 403, 'Organization required');

        return (int) $orgId;
    }

    public function pages(Request $request)
    {
        return Page::query()->where('organization_id', $this->orgId($request))->latest()->paginate(20);
    }

    public function publishPage(Request $request, Page $page, PagePublisher $publisher)
    {
        abort_unless($page->organization_id === $this->orgId($request), 403);
        abort_unless(app(ModuleRegistry::class)->isEnabled($page->organization, 'landing_pages'), 403);

        return $publisher->publish($page, $request->input('path'), $request->input('subdomain'), $request->user()->id);
    }

    public function contacts(Request $request)
    {
        return Contact::query()->where('organization_id', $this->orgId($request))->latest()->paginate(50);
    }

    public function experimentSummary(Request $request, Experiment $experiment, StatisticalSummaryService $stats)
    {
        abort_unless($experiment->organization_id === $this->orgId($request), 403);

        return $stats->summarize($experiment->load('variants'));
    }

    public function assignExperiment(Request $request, Experiment $experiment, ExperimentAllocator $allocator)
    {
        // Public-capable assignment endpoint used by published pages / tests.
        $visitorKey = $request->input('visitor_key');
        $variant = $allocator->assign($experiment, $visitorKey);

        return [
            'experiment_id' => $experiment->id,
            'variant_id' => $variant->id,
            'variant_key' => $variant->key,
            'visitor_key' => $allocator->visitorKey($visitorKey),
        ];
    }

    public function experimentEvent(Request $request, Experiment $experiment)
    {
        $data = $request->validate([
            'event_name' => 'required|string',
            'visitor_key' => 'required|string',
            'variant_id' => 'required|integer',
            'value' => 'nullable|numeric',
        ]);

        app(\App\Services\Analytics\AnalyticsTracker::class)->trackExperimentEvent(
            $experiment,
            (int) $data['variant_id'],
            $data['visitor_key'],
            $data['event_name'],
            isset($data['value']) ? (float) $data['value'] : null
        );

        return ['ok' => true];
    }
}
