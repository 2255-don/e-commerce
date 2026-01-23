<?php

namespace App\Http\Controllers\langue;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Exception;

class LanguageController extends Controller
{
    public function swap($locale)
    {
        try {
            if (! in_array($locale, ['en', 'fr'])) {
                abort(400);
            }

            session()->put('locale', $locale);
            App::setLocale($locale);

            return redirect()->back();
        } catch (Exception $e) {
            Log::error('Erreur lors du changement de langue : ' . $e->getMessage(), [
                'locale' => $locale
            ]);
            return redirect()->back();
        }
    }
}
