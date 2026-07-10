<?php

namespace App\Filament\Resources\ContactSubmissions\Pages;

use App\Filament\Resources\ContactSubmissions\ContactSubmissionResource;
use App\Models\ContactSubmission;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListContactSubmissions extends ListRecords
{
    protected static string $resource = ContactSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Xuất CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(fn (): bool => (bool) auth()->user()?->can('export_contact'))
                ->action(fn () => response()->streamDownload(function () {
                    $o = fopen('php://output', 'w');
                    fputcsv($o, ['ID', 'Tên', 'SĐT', 'Email', 'Công ty', 'Nội dung', 'Trạng thái', 'Ngày']);
                    ContactSubmission::query()->orderByDesc('id')->chunk(200, function ($rows) use ($o) {
                        foreach ($rows as $r) {
                            fputcsv($o, [$r->id, $r->name, $r->phone, $r->email, $r->company, $r->message, $r->status, (string) $r->created_at]);
                        }
                    });
                    fclose($o);
                }, 'leads-'.date('Ymd_His').'.csv', ['Content-Type' => 'text/csv'])),
        ];
    }
}
