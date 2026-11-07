<?php

namespace App\Services\Reports;

use App\Models\Experiment;
use App\Models\Organization;
use App\Services\Experiments\StatisticalSummaryService;
use ImranDev\UnicodePdf\Facades\UnicodePdf;
use Throwable;

class PdfReportService
{
    public function __construct(
        protected StatisticalSummaryService $stats
    ) {}

    public function experimentReport(Organization $organization, Experiment $experiment)
    {
        $summary = $this->stats->summarize($experiment->load('variants'));

        try {
            return UnicodePdf::locale($organization->locale ?: 'en')
                ->loadView('pdf.experiment-report', [
                    'organization' => $organization,
                    'experiment' => $experiment,
                    'summary' => $summary,
                ])
                ->name('experiment-'.$experiment->slug.'.pdf');
        } catch (Throwable $e) {
            // Fallback HTML download-friendly content when engines are unavailable.
            return response()->view('pdf.experiment-report', [
                'organization' => $organization,
                'experiment' => $experiment,
                'summary' => $summary,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
