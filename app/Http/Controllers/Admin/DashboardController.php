<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Content;
use App\Models\StageTemplate;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();

        $totalContents = Content::where('status', 'pending')->count();

        $totalTemplates = StageTemplate::count();

        $totalKarya = Content::where('user_id', Auth::id())->count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalContents',
            'totalTemplates',
            'totalKarya'
        ));
    }
}