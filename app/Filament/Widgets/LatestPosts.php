<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestPosts extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->can('view_any_post') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => Post::query()->latest('updated_at'))
            ->heading('Bài viết cập nhật gần đây')
            ->paginated([5])
            ->columns([
                TextColumn::make('title')->label('Tiêu đề')->weight('bold')->limit(50),
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Post::STATUSES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'archived' => 'gray',
                        default => 'warning',
                    }),
                TextColumn::make('author.name')->label('Tác giả')->placeholder('—'),
                TextColumn::make('updated_at')->label('Cập nhật')->since(),
            ]);
    }
}
