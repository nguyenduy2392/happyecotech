<?php

namespace App\Filament\Resources\AuditLogs\Tables;

use App\Models\AuditLog;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Thời gian')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Người dùng')
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('event')
                    ->label('Sự kiện')
                    ->badge(),
                TextColumn::make('auditable_type')
                    ->label('Đối tượng')
                    ->formatStateUsing(fn (?string $state) => $state ? class_basename($state) : '—'),
                TextColumn::make('description')
                    ->label('Mô tả')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('ip_address')
                    ->label('IP')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('event')
                    ->label('Sự kiện')
                    ->options(fn () => AuditLog::query()->distinct()->pluck('event', 'event')->all()),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([]);
    }
}
