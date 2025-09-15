<?php
namespace App\Filament\Resources;

use App\Filament\Resources\VideoResource\Pages;
use App\Models\Video;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class VideoResource extends Resource
{
    protected static ?string $model = Video::class;
    protected static ?string $navigationIcon = 'heroicon-o-play';
    protected static ?string $navigationLabel = 'Vídeos';
    protected static ?string $modelLabel = 'Vídeo';
    protected static ?string $pluralModelLabel = 'Vídeos';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações do Vídeo')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('Categoria')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) =>
                            $operation === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null
                            ),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\Textarea::make('description')
                            ->label('Descrição')
                            ->required()
                            ->maxLength(1000)
                            ->rows(3),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Configurações do Vídeo')
                    ->schema([
                        Forms\Components\TextInput::make('youtube_id')
                            ->label('ID do YouTube')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Ex: sDpFK8BtstM (da URL https://youtu.be/sDpFK8BtstM)'),

                        Forms\Components\TextInput::make('duration')
                            ->label('Duração')
                            ->required()
                            ->placeholder('Ex: 15:30')
                            ->helperText('Formato: MM:SS ou HH:MM:SS'),

                        Forms\Components\Select::make('level')
                            ->label('Nível')
                            ->options([
                                'iniciante' => 'Iniciante',
                                'intermediario' => 'Intermediário',
                                'avancado' => 'Avançado',
                                'livre' => 'Livre',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordem')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Configurações de Acesso')
                    ->schema([
                        Forms\Components\Toggle::make('is_premium')
                            ->label('Conteúdo Premium')
                            ->default(true)
                            ->helperText('Usuários precisam de plano ativo para assistir'),

                        Forms\Components\Toggle::make('is_published')
                            ->label('Publicado')
                            ->default(false)
                            ->helperText('Apenas vídeos publicados ficam visíveis'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Metadados (Opcional)')
                    ->schema([
                        Forms\Components\TextInput::make('thumbnail_url')
                            ->label('URL da Thumbnail')
                            ->url()
                            ->helperText('Se não informado, será gerado automaticamente'),

                        Forms\Components\KeyValue::make('meta')
                            ->label('Dados Extras')
                            ->keyLabel('Chave')
                            ->valueLabel('Valor'),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail_url')
                    ->label('Thumbnail')
                    ->size(60),

                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoria')
                    ->badge()
                    ->color(fn ($record) => $record->category?->color ?? '#8B5CF6')
                    ->sortable(),

                Tables\Columns\TextColumn::make('duration')
                    ->label('Duração')
                    ->sortable(),

                Tables\Columns\TextColumn::make('level')
                    ->label('Nível')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'iniciante' => 'success',
                        'intermediario' => 'warning',
                        'avancado' => 'danger',
                        'livre' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_premium')
                    ->label('Premium')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Publicado')
                    ->boolean(),

                Tables\Columns\TextColumn::make('views_count')
                    ->label('Visualizações')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('comments_count')
                    ->label('Comentários')
                    ->counts('comments')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Categoria')
                    ->relationship('category', 'name'),

                Tables\Filters\SelectFilter::make('level')
                    ->label('Nível')
                    ->options([
                        'iniciante' => 'Iniciante',
                        'intermediario' => 'Intermediário',
                        'avancado' => 'Avançado',
                        'livre' => 'Livre',
                    ]),

                Tables\Filters\TernaryFilter::make('is_premium')
                    ->label('Premium'),

                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Publicado'),
            ])
            ->actions([
                Tables\Actions\Action::make('watch')
                    ->label('Assistir')
                    ->icon('heroicon-o-play')
                    ->url(fn (Video $record): string => $record->watch_url)
                    ->openUrlInNewTab()
                    ->visible(fn (Video $record): bool => $record->is_published),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publicar')
                        ->icon('heroicon-o-eye')
                        ->action(fn (Collection $records) => $records->each->update(['is_published' => true]))
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('unpublish')
                        ->label('Despublicar')
                        ->icon('heroicon-o-eye-slash')
                        ->action(fn (Collection $records) => $records->each->update(['is_published' => false]))
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informações do Vídeo')
                    ->schema([
                        Infolists\Components\ImageEntry::make('thumbnail_url')
                            ->label('Thumbnail')
                            ->height(200),

                        Infolists\Components\TextEntry::make('title')
                            ->label('Título')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                            ->weight('bold'),

                        Infolists\Components\TextEntry::make('description')
                            ->label('Descrição')
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('category.name')
                            ->label('Categoria')
                            ->badge(),

                        Infolists\Components\TextEntry::make('level')
                            ->label('Nível')
                            ->badge(),

                        Infolists\Components\TextEntry::make('duration')
                            ->label('Duração'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Configurações')
                    ->schema([
                        Infolists\Components\IconEntry::make('is_premium')
                            ->label('Premium')
                            ->boolean(),

                        Infolists\Components\IconEntry::make('is_published')
                            ->label('Publicado')
                            ->boolean(),

                        Infolists\Components\TextEntry::make('sort_order')
                            ->label('Ordem'),

                        Infolists\Components\TextEntry::make('youtube_id')
                            ->label('ID YouTube')
                            ->copyable(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Estatísticas')
                    ->schema([
                        Infolists\Components\TextEntry::make('views_count')
                            ->label('Visualizações')
                            ->numeric(),

                        Infolists\Components\TextEntry::make('comments_count')
                            ->label('Comentários')
                            ->getStateUsing(fn ($record) => $record->comments()->count()),

                        Infolists\Components\TextEntry::make('completion_rate')
                            ->label('Taxa de Conclusão')
                            ->getStateUsing(fn ($record) => $record->views()->where('completed', true)->count() . '/' . $record->views()->count()),

                        Infolists\Components\TextEntry::make('avg_watch_time')
                            ->label('Tempo Médio Assistido')
                            ->getStateUsing(fn ($record) => $record->views()->avg('watch_time') ? round($record->views()->avg('watch_time')) . 's' : 'N/A'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVideos::route('/'),
            'create' => Pages\CreateVideo::route('/create'),
            //'view' => Pages\ViewVideo::route('/{record}'),
            'edit' => Pages\EditVideo::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::published()->count();
    }
}
