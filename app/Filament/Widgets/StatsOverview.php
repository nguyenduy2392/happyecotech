<?php

namespace App\Filament\Widgets;

use App\Models\ContactSubmission;
use App\Models\Post;
use App\Models\Service;
use App\Models\TeamMember;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Bài viết đã đăng', Post::where('status', 'published')->count())
                ->description(Post::where('status', 'draft')->count().' bản nháp')
                ->descriptionIcon('heroicon-o-pencil-square')
                ->color('success'),
            Stat::make('Dịch vụ đang hiển thị', Service::where('status', 'published')->count())
                ->descriptionIcon('heroicon-o-rectangle-stack')
                ->color('primary'),
            Stat::make('Thành viên đội ngũ', TeamMember::where('status', 'published')->count())
                ->descriptionIcon('heroicon-o-user-group')
                ->color('info'),
            Stat::make('Lead mới', ContactSubmission::where('status', 'new')->count())
                ->description('Cần xử lý')
                ->descriptionIcon('heroicon-o-inbox-arrow-down')
                ->color('warning'),
        ];
    }
}
