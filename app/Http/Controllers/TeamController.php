<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use App\Support\CompanyProfile;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    public function index()
    {
        $members = TeamMember::query()->published()->with('photo')->ordered()->get();
        $people = $members->filter(CompanyProfile::isChiefExecutive(...))->map(function (TeamMember $member) {
            $name = Str::squish((string) $member->name);
            if ($name === '' || in_array(Str::lower(Str::ascii($name)), ['tong giam doc', 'ceo', 'giam doc dieu hanh', 'chua cap nhat'], true)
                || Str::lower($name) === Str::lower(Str::squish((string) $member->position))) {
                return null;
            }

            return ['name' => $name, 'jobTitle' => $member->position, 'image' => $member->photo?->url];
        })->filter()->values()->all();

        return view('public.team.index', [
            'members' => $members,
            'seo' => he_seo([
                'title' => 'Đội ngũ',
                'people' => $people,
            ]),
        ]);
    }
}
