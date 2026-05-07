<?php

namespace App\Http\Controllers;

use App\Models\Construct;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KbController extends Controller
{
    public function show(Request $request, string $language, string $slug): View
    {
        $languageId = Language::query()->where('code', $language)->value('id');
        abort_if($languageId === null, 404);

        $construct = Construct::query()
            ->where('language_id', $languageId)
            ->where('slug', $slug)
            ->with(['language', 'snippets', 'links', 'aliases'])
            ->first();

        abort_if($construct === null, 404);

        return view('kb.show', [
            'construct' => $construct,
        ]);
    }
}
