<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\PageTemplate;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MarketingController extends Controller
{
    public function home()
    {
        return view('marketing.home');
    }

    public function page(string $page)
    {
        $allowed = [
            'features', 'builder', 'funnels', 'ab-testing', 'analytics', 'ai',
            'templates', 'pricing', 'use-cases', 'about', 'contact',
        ];

        abort_unless(in_array($page, $allowed, true), 404);

        return view('marketing.'.$page, [
            'plans' => $page === 'pricing' ? Plan::query()->where('is_active', true)->orderBy('sort_order')->get() : collect(),
            'templates' => $page === 'templates' ? PageTemplate::query()->where('is_marketplace', true)->latest()->take(12)->get() : collect(),
        ]);
    }

    public function blog()
    {
        return view('marketing.blog', [
            'posts' => BlogPost::query()->where('status', 'published')->latest('published_at')->get(),
        ]);
    }

    public function blogShow(string $slug)
    {
        $post = BlogPost::query()->where('slug', $slug)->where('status', 'published')->firstOrFail();

        return view('marketing.blog-show', compact('post'));
    }

    public function legal(string $page)
    {
        abort_unless(in_array($page, ['privacy', 'terms'], true), 404);

        return view('marketing.legal', ['page' => $page]);
    }

    public function contactSubmit(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email',
            'message' => 'required|string|max:5000',
        ]);

        Mail::raw("Contact from {$data['name']} <{$data['email']}>\n\n{$data['message']}", function ($message) {
            $message->to(config('mail.from.address'))->subject('Website contact');
        });

        return back()->with('success', 'Thanks — we will reply shortly.');
    }
}
