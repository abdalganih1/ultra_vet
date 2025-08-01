<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\StrayPet;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class PageController extends Controller
{
    public function landing()
    {
        $stats = [
            'total_pets' => StrayPet::where('data_entered_status', true)->count(),
            // Add more stats as needed, e.g., adopted pets if you add that feature
        ];

        $recentPets = StrayPet::where('data_entered_status', true)
                                ->whereNotNull('image_path')
                                ->latest()
                                ->take(10)
                                ->get();

        return view('landing', compact('stats', 'recentPets'));
    }

    public function about()
    {
        return view('pages.about');
    }


    public function team()
    {
        return view('pages.team');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    /**
     * Switch the application language.
     *
     * @param string $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchLang($locale)
    {
        if (in_array($locale, ['ar', 'en'])) {
            Session::put('locale', $locale);
        }
        return Redirect::back();
    }
}