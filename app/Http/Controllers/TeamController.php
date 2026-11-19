<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use App\Support\CompanyProfile;

class TeamController extends Controller
{
    public function index()
    {
        $members = TeamMember::query()->published()->with('photo')->ordered()->get();
        $people = array_values(array_filter([CompanyProfile::person($members->firstWhere('slug', CompanyProfile::CEO_SLUG))]));

        return view('public.team.index', [
            'members' => $members,
            'seo' => he_seo([
                'title' => 'Đội ngũ',
                'people' => $people,
            ]),
        ]);
    }
}
