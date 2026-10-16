<?php

namespace App\Filament\Resources\Aspirations;

use App\Filament\Resources\Aspirations\Pages\CreateAspiration;
use App\Filament\Resources\Aspirations\Pages\EditAspiration;
use App\Filament\Resources\Aspirations\Pages\ListAspirations;
use App\Filament\Resources\Aspirations\Schemas\AspirationForm;
use App\Filament\Resources\Aspirations\Tables\AspirationsTable;
use App\Models\Aspiration;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AspirationResource extends Resource
{
    protected static ?string $model = Aspiration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;
    protected static ?string $navigationLabel = 'Aspirasi Suara Anak';
    protected static ?string $modelLabel = 'Aspirasi Suara Anak';
    protected static ?string $pluralModelLabel = 'Aspirasi Suara Anak';
    protected static \UnitEnum|string|null $navigationGroup = 'Partisipasi Anak & Warga';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'draft')->orWhere('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Jumlah aspirasi baru yang menunggu ditinjau';
    }

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return AspirationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AspirationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAspirations::route('/'),
            'create' => CreateAspiration::route('/create'),
            'edit' => EditAspiration::route('/{record}/edit'),
        ];
    }
}
