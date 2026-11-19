<?php

namespace Database\Seeders;

use App\Support\CompanyProfile;
use Illuminate\Database\Seeder;

class TeamMemberSeeder extends Seeder
{
    public function run(): void
    {
        CompanyProfile::attachPortrait(CompanyProfile::chiefExecutive());
    }
}
