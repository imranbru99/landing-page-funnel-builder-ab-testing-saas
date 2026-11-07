<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Services\Crm\FormCaptureService;
use Illuminate\Http\Request;

class FormSubmitController extends Controller
{
    public function __invoke(Request $request, string $slug, FormCaptureService $capture)
    {
        $form = Form::query()->where('slug', $slug)->where('is_active', true)->firstOrFail();

        $payload = $request->except(['_token', 'page_id']);
        $submission = $capture->capture($form, $payload, $request, $request->integer('page_id') ?: null);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'submission_id' => $submission->id,
            ]);
        }

        return redirect()->back()->with('success', 'Thanks! We received your submission.');
    }
}
