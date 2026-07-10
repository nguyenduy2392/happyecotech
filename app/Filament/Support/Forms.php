<?php

namespace App\Filament\Support;

use App\Models\MediaAsset;
use Filament\Forms\Components\Select;

class Forms
{
    /**
     * Select chọn media từ Media Library, lưu FK *_media_id qua quan hệ belongsTo.
     *
     * @param  string  $field  cột FK, ví dụ 'icon_media_id'
     * @param  string  $relation  tên quan hệ belongsTo trên model, ví dụ 'icon'
     */
    public static function media(string $field, string $relation, string $label): Select
    {
        return Select::make($field)
            ->label($label)
            ->relationship($relation, 'file_name')
            ->getOptionLabelFromRecordUsing(
                fn (MediaAsset $record): string => trim(($record->alt ? $record->alt.' — ' : '').$record->file_name)
            )
            ->searchable()
            ->preload()
            ->nullable();
    }
}
