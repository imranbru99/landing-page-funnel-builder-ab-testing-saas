<?php

namespace App\Services\Automation;

use App\Models\Contact;
use App\Models\Tag;
use App\Models\Workflow;
use App\Models\WorkflowRun;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WorkflowRunner
{
    public function run(Workflow $workflow, array $context = []): WorkflowRun
    {
        $run = WorkflowRun::query()->create([
            'workflow_id' => $workflow->id,
            'organization_id' => $workflow->organization_id,
            'status' => 'running',
            'context' => $context,
            'started_at' => now(),
        ]);

        $results = [];

        try {
            foreach ($workflow->actions ?? [] as $index => $action) {
                $results[] = $this->executeAction($workflow, $action, $context);
            }

            $run->update([
                'status' => 'completed',
                'result' => $results,
                'finished_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Workflow failed', [
                'workflow_id' => $workflow->id,
                'error' => $e->getMessage(),
            ]);

            $run->update([
                'status' => 'failed',
                'result' => ['error' => $e->getMessage(), 'steps' => $results],
                'finished_at' => now(),
            ]);
        }

        return $run->fresh();
    }

    protected function executeAction(Workflow $workflow, array $action, array $context): array
    {
        $type = $action['type'] ?? 'noop';

        return match ($type) {
            'tag_contact' => $this->tagContact($workflow, $action, $context),
            'update_contact_status' => $this->updateContactStatus($action, $context),
            'send_email' => $this->sendEmail($action, $context),
            'log' => ['type' => 'log', 'message' => $action['message'] ?? 'ok'],
            default => ['type' => $type, 'status' => 'skipped'],
        };
    }

    protected function tagContact(Workflow $workflow, array $action, array $context): array
    {
        $contactId = $context['contact_id'] ?? null;
        if (! $contactId) {
            return ['type' => 'tag_contact', 'status' => 'skipped'];
        }

        $tagName = $action['tag'] ?? 'automation';
        $tag = Tag::query()->firstOrCreate(
            ['organization_id' => $workflow->organization_id, 'name' => $tagName],
            ['color' => '#0F766E']
        );

        $contact = Contact::query()->find($contactId);
        $contact?->tags()->syncWithoutDetaching([$tag->id]);

        return ['type' => 'tag_contact', 'status' => 'ok', 'tag' => $tagName];
    }

    protected function updateContactStatus(array $action, array $context): array
    {
        $contactId = $context['contact_id'] ?? null;
        if (! $contactId) {
            return ['type' => 'update_contact_status', 'status' => 'skipped'];
        }

        Contact::query()->whereKey($contactId)->update([
            'status' => $action['status'] ?? 'qualified',
            'last_activity_at' => now(),
        ]);

        return ['type' => 'update_contact_status', 'status' => 'ok'];
    }

    protected function sendEmail(array $action, array $context): array
    {
        $to = $action['to'] ?? data_get($context, 'payload.email');
        if (! $to) {
            return ['type' => 'send_email', 'status' => 'skipped'];
        }

        // Sandbox-safe: log channel in local/demo
        Mail::raw($action['body'] ?? 'Thanks for your submission.', function ($message) use ($to, $action) {
            $message->to($to)->subject($action['subject'] ?? 'Thanks for reaching out');
        });

        return ['type' => 'send_email', 'status' => 'queued', 'to' => $to];
    }
}
