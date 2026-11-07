<?php

use App\Http\Controllers\Marketing\MarketingController;
use App\Http\Controllers\Public\FormSubmitController;
use App\Http\Controllers\Public\PublishedPageController;
use App\Livewire\FunnelBuilder\FunnelEditor;
use App\Livewire\PageBuilder\PageEditor;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarketingController::class, 'home'])->name('home');

foreach ([
    'features', 'builder', 'funnels', 'ab-testing', 'analytics', 'ai',
    'templates', 'pricing', 'use-cases', 'about', 'contact',
] as $page) {
    Route::get('/'.$page, fn () => app(MarketingController::class)->page($page))->name($page);
}

Route::post('/contact', [MarketingController::class, 'contactSubmit'])->name('contact.submit');
Route::get('/blog', [MarketingController::class, 'blog'])->name('blog');
Route::get('/blog/{slug}', [MarketingController::class, 'blogShow'])->name('blog.show');
Route::get('/legal/{page}', [MarketingController::class, 'legal'])->name('legal');

Route::get('/p/{subdomain}', [PublishedPageController::class, 'subdomain'])->name('pages.subdomain');
Route::get('/sites/{path}', [PublishedPageController::class, 'path'])->where('path', '.*')->name('pages.path');
Route::post('/forms/{slug}/submit', FormSubmitController::class)->name('forms.submit');

Route::middleware(['auth'])->group(function () {
    Route::get('/editor/pages/{page}', PageEditor::class)->name('editor.pages');
    Route::get('/editor/funnels/{funnel}', FunnelEditor::class)->name('editor.funnels');
});
