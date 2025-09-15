<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Usuários';
    protected static ?string $modelLabel = 'Usuário';
    protected static ?string $pluralModelLabel = 'Usuários';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações Pessoais')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\Textarea::make('bio')
                            ->label('Biografia')
                            ->maxLength(1000)
                            ->rows(3),

                        Forms\Components\FileUpload::make('avatar')
                            ->label('Avatar')
                            ->image()
                            ->directory('avatars')
                            ->disk('public')
                            ->imageEditor(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Configurações')
                    ->schema([
                        Forms\Components\Select::make('current_plan_id')
                            ->label('Plano Atual')
                            ->relationship('currentPlan', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\DateTimePicker::make('plan_expires_at')
                            ->label('Plano Expira em')
                            ->native(false),

                        Forms\Components\Toggle::make('is_admin')
                            ->label('Administrador'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Ativo')
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Datas')
                    ->schema([
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label('E-mail Verificado em')
                            ->native(false),

                        Forms\Components\DateTimePicker::make('last_activity_at')
                            ->label('Última Atividade')
                            ->native(false)
                            ->disabled(),
                    ])
                    ->columns(2)
                    ->visibleOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_url')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl(fn($record) => $record->avatar_url),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('currentPlan.name')
                    ->label('Plano')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Plano Mensal' => 'info',
                        'Plano Trimestral' => 'success',
                        'Plano Anual' => 'warning',
                        default => 'gray',
                    })
                    ->default('Gratuito'),

                Tables\Columns\TextColumn::make('plan_expires_at')
                    ->label('Plano Expira')
                    ->dateTime('d/m/Y')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_admin')
                    ->label('Admin')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Cadastrado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('last_activity_at')
                    ->label('Última Atividade')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('current_plan_id')
                    ->label('Plano')
                    ->relationship('currentPlan', 'name'),

                Tables\Filters\TernaryFilter::make('is_admin')
                    ->label('Administrador'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Ativo'),

                Tables\Filters\Filter::make('subscribed')
                    ->label('Com Plano Ativo')
                    ->query(fn (Builder $query): Builder => $query->subscribed()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informações do Usuário')
                    ->schema([
                        Infolists\Components\ImageEntry::make('avatar_url')
                            ->label('Avatar')
                            ->circular(),

                        Infolists\Components\TextEntry::make('name')
                            ->label('Nome'),

                        Infolists\Components\TextEntry::make('email')
                            ->label('E-mail')
                            ->copyable(),

                        Infolists\Components\TextEntry::make('bio')
                            ->label('Biografia')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Plano e Assinatura')
                    ->schema([
                        Infolists\Components\TextEntry::make('currentPlan.name')
                            ->label('Plano Atual')
                            ->badge()
                            ->default('Gratuito'),

                        Infolists\Components\TextEntry::make('plan_expires_at')
                            ->label('Plano Expira em')
                            ->dateTime('d/m/Y H:i'),

                        Infolists\Components\TextEntry::make('days_remaining')
                            ->label('Dias Restantes')
                            ->badge()
                            ->color(fn (string $state): string => match (true) {
                                $state <= 7 => 'danger',
                                $state <= 30 => 'warning',
                                default => 'success',
                            }),

                        Infolists\Components\TextEntry::make('subscription_status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'expiring' => 'warning',
                                'admin' => 'info',
                                default => 'gray',
                            }),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Estatísticas')
                    ->schema([
                        Infolists\Components\TextEntry::make('videoViews')
                            ->label('Vídeos Assistidos')
                            ->getStateUsing(fn ($record) => $record->videoViews()->count()),

                        Infolists\Components\TextEntry::make('comments')
                            ->label('Comentários')
                            ->getStateUsing(fn ($record) => $record->comments()->count()),

                        Infolists\Components\TextEntry::make('total_watch_time')
                            ->label('Tempo Total Assistido')
                            ->getStateUsing(fn ($record) => $record->formatWatchTime($record->videoViews()->sum('watch_time'))),

                        Infolists\Components\TextEntry::make('completed_videos')
                            ->label('Vídeos Completos')
                            ->getStateUsing(fn ($record) => $record->videoViews()->where('completed', true)->count()),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Configurações')
                    ->schema([
                        Infolists\Components\IconEntry::make('is_admin')
                            ->label('Administrador')
                            ->boolean(),

                        Infolists\Components\IconEntry::make('is_active')
                            ->label('Ativo')
                            ->boolean(),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Cadastrado em')
                            ->dateTime('d/m/Y H:i'),

                        Infolists\Components\TextEntry::make('last_activity_at')
                            ->label('Última Atividade')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('Nunca'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
           // 'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
