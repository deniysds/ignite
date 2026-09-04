<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    /**
     * Switch application locale and redirect back.
     */
    public function switch(string $lang, Request $request): RedirectResponse
    {
        if (in_array($lang, SetLocale::SUPPORTED_LOCALES, true)) {
            Session::put('locale', $lang);
        }

        return redirect()->back();
    }
}
