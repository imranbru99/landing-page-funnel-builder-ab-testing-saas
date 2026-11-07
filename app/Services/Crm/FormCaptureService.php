<?php

namespace App\Services\Crm;

use App\Jobs\RunWorkflowJob;
use App\Models\Contact;
use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\Pipeline;
use App\Models\Workflow;
use App\Services\Analytics\AnalyticsTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class FormCaptureService
{
    public function __construct(
        protected AnalyticsTracker $analytics
    ) {}

    public function capture(Form $form, array $payload, Request $request, ?int $pageId = null): FormSubmission
    {
        if (! $form->is_active) {
            throw new \RuntimeException('Form is inactive.');
        }

        $email = Arr::get($payload, 'email');
        $contact = null;

        if ($email) {
            $pipeline = Pipeline::query()
                ->where('organization_id', $form->organization_id)
                ->where('is_default', true)
                ->first();

            $contact = Contact::query()->firstOrCreate(
                [
                    'organization_id' => $form->organization_id,
                    'email' => strtolower($email),
                ],
                [
                    'project_id' => $form->project_id,
                    'pipeline_id' => $pipeline?->id,
                    'pipeline_stage_id' => $pipeline?->stages()->orderBy('position')->value('id'),
                    'first_name' => Arr::get($payload, 'first_name') ?? Arr::get($payload, 'name'),
                    'last_name' => Arr::get($payload, 'last_name'),
                    'phone' => Arr::get($payload, 'phone'),
                    'company' => Arr::get($payload, 'company'),
                    'source' => 'form:'.$form->slug,
                    'status' => 'new',
                    'last_activity_at' => now(),
                ]
            );

            $contact->update(['last_activity_at' => now()]);
        }

        $submission = FormSubmission::query()->create([
            'form_id' => $form->id,
            'organization_id' => $form->organization_id,
            'contact_id' => $contact?->id,
            'page_id' => $pageId,
            'visitor_key' => $request->cookie('lf_visitor'),
            'payload' => $payload,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);

        $this->analytics->track([
            'organization_id' => $form->organization_id,
            'project_id' => $form->project_id,
            'page_id' => $pageId,
            'visitor_key' => $submission->visitor_key,
            'event_name' => 'form_submitted',
            'properties' => [
                'form_id' => $form->id,
                'submission_id' => $submission->id,
            ],
        ]);

        $workflows = Workflow::query()
            ->where('organization_id', $form->organization_id)
            ->where('status', 'active')
            ->where('trigger_type', 'form_submitted')
            ->get();

        foreach ($workflows as $workflow) {
            $configFormId = data_get($workflow->trigger_config, 'form_id');
            if ($configFormId && (int) $configFormId !== $form->id) {
                continue;
            }

            RunWorkflowJob::dispatch($workflow->id, [
                'form_id' => $form->id,
                'submission_id' => $submission->id,
                'contact_id' => $contact?->id,
                'payload' => $payload,
            ]);
        }

        return $submission;
    }
}
