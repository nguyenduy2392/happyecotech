<?php

namespace Database\Seeders;

use App\Support\CompanyProfile;
use Illuminate\Database\Seeder;

class TeamMemberSeeder extends Seeder
{
    public function run(): void
    {
        $leader = CompanyProfile::chiefExecutive();
        if ($leader?->slug === CompanyProfile::CEO_SLUG && $leader->name === CompanyProfile::CEO_ROLE) {
            // The old seed used the role as a placeholder for the missing name.
            $leader->update(['name' => CompanyProfile::CEO_NAME]);
        }
        CompanyProfile::attachPortrait($leader);
    }
}
