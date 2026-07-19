<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;

class SupportController extends Controller
{
    // index view
    public function index()
    {
        $links = [
            'live-chat' => 'https://support.techics.com/',
            'support-tickets' => 'https://support.techics.com/',
            'latest-announcements' => 'https://support.techics.com/',
            'faq' => 'https://www.techics.net/site/faq/',
            'documentation' => 'https://support.techics.com/',
            'report-problem' => 'https://support.techics.com/',
        ];

        return view('marketing.support', compact('links'));
    }
}
