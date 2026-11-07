<?php

namespace App\Services\Ai;

use App\Models\AiGeneration;
use App\Models\Organization;
use App\Models\User;
use ImranDevBd\AiHub\Facades\AIHub;
use Throwable;

class AiMarketingAssistant
{
    public function generate(Organization $organization, string $type, array $input, ?User $user = null): AiGeneration
    {
        $prompt = $this->buildPrompt($type, $input);

        try {
            // Prefer fake/mock responses when no provider keys are configured.
            if (! $this->hasConfiguredProvider()) {
                AIHub::fake([
                    AIHub::response($this->mockContent($type, $input)),
                ]);
            }

            $response = AIHub::prompt($prompt)->send();
            $content = is_object($response) && isset($response->content)
                ? (string) $response->content
                : (string) $response;

            return AiGeneration::query()->create([
                'organization_id' => $organization->id,
                'user_id' => $user?->id,
                'type' => $type,
                'prompt' => ['text' => $prompt, 'input' => $input],
                'response' => ['content' => $content],
                'status' => 'completed',
                'approved' => false,
            ]);
        } catch (Throwable $e) {
            return AiGeneration::query()->create([
                'organization_id' => $organization->id,
                'user_id' => $user?->id,
                'type' => $type,
                'prompt' => ['text' => $prompt, 'input' => $input],
                'response' => ['content' => $this->mockContent($type, $input), 'error' => $e->getMessage(), 'mocked' => true],
                'status' => 'completed',
                'approved' => false,
            ]);
        }
    }

    public function hasConfiguredProvider(): bool
    {
        $keys = [
            env('OPENAI_API_KEY'),
            env('GEMINI_API_KEY'),
            env('ANTHROPIC_API_KEY'),
            env('AI_HUB_API_KEY'),
        ];

        return collect($keys)->filter(fn ($v) => filled($v))->isNotEmpty();
    }

    protected function buildPrompt(string $type, array $input): string
    {
        $topic = $input['topic'] ?? $input['product'] ?? 'our offer';

        return match ($type) {
            'headline' => "Write 5 high-converting landing page headlines for: {$topic}",
            'cta' => "Suggest 8 CTA button texts for: {$topic}",
            'seo' => "Generate SEO title, meta description, and keywords for: {$topic}",
            'funnel' => "Recommend a 5-step sales funnel for: {$topic}",
            'ab_variant' => "Create an A/B variant headline and CTA for: {$topic}",
            'email' => "Write a short nurture email for leads interested in: {$topic}",
            default => "Write marketing copy for: {$topic}",
        };
    }

    protected function mockContent(string $type, array $input): string
    {
        $topic = $input['topic'] ?? 'Growth';

        return match ($type) {
            'headline' => "Unlock {$topic} in 14 Days\nThe Smarter Way to Scale {$topic}\nStop Guessing. Start Converting.",
            'cta' => "Start Free Trial\nGet My Demo\nBuild My Funnel\nSee Templates",
            'seo' => "Title: {$topic} Landing Pages That Convert\nDescription: Build, test, and optimize high-converting pages for {$topic}.\nKeywords: {$topic}, landing pages, funnels, A/B testing",
            'funnel' => "1) Awareness landing page\n2) Lead magnet form\n3) Nurture email\n4) Sales page\n5) Checkout / thank you",
            'ab_variant' => "Variant B Headline: {$topic} Without the Busywork\nCTA: Try It Free",
            'email' => "Subject: Your {$topic} playbook is ready\nBody: Thanks for opting in. Here's the next step to launch your first experiment.",
            default => "Draft copy for {$topic} ready for human approval.",
        };
    }
}
