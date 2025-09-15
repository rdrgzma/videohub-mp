<?php
namespace App\Filament\Resources;

use App\Filament\Resources\PlanResource\Pages;
use App\Models\Plan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlanResource extends Resource
{
protected static ?string $model = Plan::class;
protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
protected static ?string $navigationLabel = 'Planos';
protected static ?string $modelLabel = 'Plano';
protected static ?string $pluralModelLabel = 'Planos';
protected static ?int $navigationSort = 4;

public static function form(Form $form): Form
{
return $form
->schema([
Forms\Components\Section::make('Informações do Plano')
->schema([
Forms\Components\TextInput::make('name')
->label('Nome')
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
->maxLength(1000)
->rows(3),
])
->columns(2),

Forms\Components\Section::make('Preço e Duração')
->schema([
Forms\Components\TextInput::make('price')
->label('Preço')
->required()
->numeric()
->prefix('R$')
->step(0.01),

Forms\Components\Select::make('billing_cycle')
->label('Ciclo de Cobrança')
->options([
'monthly' => 'Mensal',
'quarterly' => 'Trimestral',
'yearly' => 'Anual',
])
->required(),

Forms\Components\TextInput::make('duration_months')
->label('Duração (meses)')
->required()
->numeric()
->minValue(1),

Forms\Components\TextInput::make('sort_order')
->label('Ordem')
->numeric()
->default(0),
])
->columns(2),

Forms\Components\Section::make('Características')
->schema([
Forms\Components\Repeater::make('features')
->label('Recursos Inclusos')
->simple(
Forms\Components\TextInput::make('feature')
->label('Recurso')
->required()
)
->defaultItems(3)
->addActionLabel('Adicionar Recurso'),
]),

Forms\Components\Section::make('Configurações')
->schema([
Forms\Components\Toggle::make('is_popular')
->label('Plano Popular')
->helperText('Será destacado na página de planos'),

Forms\Components\Toggle::make('is_active')
->label('Ativo')
->default(true),
])
->columns(2),
]);
}

public static function table(Table $table): Table
{
return $table
->columns([
Tables\Columns\TextColumn::make('name')
->label('Nome')
->searchable()
->sortable(),

Tables\Columns\TextColumn::make('price')
->label('Preço')
->money('BRL')
->sortable(),

Tables\Columns\TextColumn::make('billing_cycle')
->label('Ciclo')
->badge()
->color(fn (string $state): string => match ($state) {
'monthly' => 'info',
'quarterly' => 'success',
'yearly' => 'warning',
default => 'gray',
})
->formatStateUsing(fn (string $state): string => match ($state) {
'monthly' => 'Mensal',
'quarterly' => 'Trimestral',
'yearly' => 'Anual',
default => $state,
}),

Tables\Columns\TextColumn::make('duration_months')
->label('Duração')
->suffix(' meses')
->sortable(),

Tables\Columns\TextColumn::make('users_count')
->label('Assinantes')
->counts('users')
->sortable(),

Tables\Columns\IconColumn::make('is_popular')
->label('Popular')
->boolean(),

Tables\Columns\IconColumn::make('is_active')
->label('Ativo')
->boolean(),

Tables\Columns\TextColumn::make('sort_order')
->label('Ordem')
->sortable(),
])
->filters([
Tables\Filters\SelectFilter::make('billing_cycle')
->label('Ciclo de Cobrança')
->options([
'monthly' => 'Mensal',
'quarterly' => 'Trimestral',
'yearly' => 'Anual',
]),

Tables\Filters\TernaryFilter::make('is_popular')
->label('Popular'),

Tables\Filters\TernaryFilter::make('is_active')
->label('Ativo'),
])
->actions([
Tables\Actions\EditAction::make(),
Tables\Actions\DeleteAction::make(),
])
->bulkActions([
Tables\Actions\BulkActionGroup::make([
Tables\Actions\DeleteBulkAction::make(),
]),
])
->defaultSort('sort_order')
->reorderable('sort_order');
}

public static function getPages(): array
{
return [
'index' => Pages\ListPlans::route('/'),
'create' => Pages\CreatePlan::route('/create'),
'edit' => Pages\EditPlan::route('/{record}/edit'),
];
}

public static function getNavigationBadge(): ?string
{
return static::getModel()::active()->count();
}
}
