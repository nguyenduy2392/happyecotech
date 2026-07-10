<?php

namespace App\Filament\Resources\TeamMembers\Schemas;

use App\Filament\Support\Forms;
use App\Models\TeamMember;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TeamMemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Họ và tên')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug((string) $state))),
                        TextInput::make('slug')
                            ->label('Đường dẫn')
                            ->maxLength(255),
                        TextInput::make('position')
                            ->label('Chức danh')
                            ->maxLength(255),
                        TextInput::make('group')
                            ->label('Nhóm/Phòng ban')
                            ->maxLength(255),
                        Textarea::make('short_bio')
                            ->label('Giới thiệu ngắn')
                            ->rows(3)
                            ->columnSpanFull(),
                        RichEditor::make('bio')
                            ->label('Tiểu sử')
                            ->columnSpanFull(),
                    ]),

                Section::make('Ảnh & Liên hệ')
                    ->columns(2)
                    ->schema([
                        Forms::media('photo_media_id', 'photo', 'Ảnh đại diện'),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('Số điện thoại')
                            ->tel()
                            ->maxLength(255),
                        KeyValue::make('socials')
                            ->label('Mạng xã hội')
                            ->keyLabel('Nền tảng')
                            ->valueLabel('URL')
                            ->columnSpanFull(),
                    ]),

                Section::make('Hiển thị')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label('Trạng thái')
                            ->options(TeamMember::STATUSES)
                            ->default('draft')
                            ->required(),
                        Toggle::make('is_featured')
                            ->label('Nổi bật'),
                        TextInput::make('sort_order')
                            ->label('Thứ tự')
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
