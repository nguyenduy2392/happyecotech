<?php

namespace App\Filament\Widgets;

use App\Models\ContactSubmission;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestLeads extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->can('view_any_contact') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => ContactSubmission::query()->latest())
            ->heading('Lead mới nhất')
            ->paginated([5])
            ->columns([
                TextColumn::make('name')->label('Họ tên')->weight('bold'),
                TextColumn::make('phone')->label('Điện thoại'),
                TextColumn::make('service.name')->label('Dịch vụ')->placeholder('—'),
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ContactSubmission::STATUSES[$state] ?? $state),
                TextColumn::make('created_at')->label('Nhận lúc')->since(),
            ]);
    }
}
