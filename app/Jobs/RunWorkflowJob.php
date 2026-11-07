<?php

namespace App\Jobs;

use App\Models\Workflow;
use App\Services\Automation\WorkflowRunner;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RunWorkflowJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $workflowId,
        public array $context = []
    ) {}

    public function handle(WorkflowRunner $runner): void
    {
        $workflow = Workflow::query()->find($this->workflowId);
        if (! $workflow) {
            return;
        }

        $runner->run($workflow, $this->context);
    }
}
