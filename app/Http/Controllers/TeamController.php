<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;

class TeamController extends Controller
{
    public function index()
    {
        $members = TeamMember::query()->published()->with('photo')->ordered()->get();

        return view('public.team.index', [
            'members' => $members,
            'seo' => he_seo([
                'title' => 'Đội ngũ',
                'description' => 'Đội ngũ nhân sự của Happy Ecotech.',
            ]),
        ]);
    }
}
