<?php

namespace App\Filament\Resources\Characters;

use App\Filament\Resources\Characters\Pages\ListCharacters;
use App\Filament\Resources\Characters\Pages\ViewCharacter;
use App\Filament\Resources\Characters\Schemas\CharacterForm;
use App\Filament\Resources\Characters\Tables\CharactersTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use SilkPanel\SilkroadModels\Models\Shard\AbstractChar;

class CharacterResource extends Resource
{
    protected static ?string $model = null;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string | \UnitEnum | null $navigationGroup = 'Silkroad';

    protected static ?string $recordTitleAttribute = 'CharName16';

    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return __('filament/characters.navigation');
    }

    public static function getModel(): string
    {
        return get_class(resolve(AbstractChar::class));
    }

    public static function form(Schema $schema): Schema
    {
        return CharacterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CharactersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['CharName16', 'CharID'];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCharacters::route('/'),
            'view' => ViewCharacter::route('/{record}'),
        ];
    }
}
