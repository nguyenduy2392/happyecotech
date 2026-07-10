<?php

namespace App\Filament\Resources\ContactSubmissions\Schemas;

use App\Models\ContactSubmission;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactSubmissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin liên hệ')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Họ tên')
                            ->disabled(),
                        TextInput::make('phone')
                            ->label('Số điện thoại')
                            ->disabled(),
                        TextInput::make('email')
                            ->label('Email')
                            ->disabled(),
                        TextInput::make('company')
                            ->label('Công ty')
                            ->disabled(),
                        TextInput::make('service_interest')
                            ->label('Dịch vụ quan tâm')
                            ->disabled(),
                        Textarea::make('message')
                            ->label('Nội dung')
                            ->disabled()
                            ->columnSpanFull(),
                    ]),
                Section::make('Xử lý')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label('Trạng thái')
                            ->options(ContactSubmission::STATUSES)
                            ->default('new')
                            ->required(),
                        Select::make('assigned_to')
                            ->label('Người phụ trách')
                            ->relationship('assignee', 'name')
                            ->searchable()
                            ->preload(),
                    ]),
            ]);
    }
}
