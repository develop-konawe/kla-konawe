<?php

namespace App\Filament\Resources\ForumReplies;

use App\Filament\Resources\ForumReplies\Pages\CreateForumReply;
use App\Filament\Resources\ForumReplies\Pages\EditForumReply;
use App\Filament\Resources\ForumReplies\Pages\ListForumReplies;
use App\Filament\Resources\ForumReplies\Schemas\ForumReplyForm;
use App\Filament\Resources\ForumReplies\Tables\ForumRepliesTable;
use App\Models\ForumReply;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ForumReplyResource extends Resource
{
    protected static ?string $model = ForumReply::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUturnLeft;
    protected static ?string $navigationLabel = 'Balasan Diskusi';
    protected static ?string $modelLabel = 'Balasan Diskusi Forum';
    protected static ?string $pluralModelLabel = 'Balasan Diskusi Forum';
    protected static \UnitEnum|string|null $navigationGroup = 'Partisipasi Anak & Warga';
    protected static ?string $navigationParentItem = 'Forum Anak (Diskusi)';
    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return ForumReplyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ForumRepliesTable::configure($table);
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
            'index' => ListForumReplies::route('/'),
            'create' => CreateForumReply::route('/create'),
            'edit' => EditForumReply::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
