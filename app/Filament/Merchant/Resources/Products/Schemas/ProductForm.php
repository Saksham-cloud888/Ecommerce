<?php

namespace App\Filament\Merchant\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Product information')
                            ->description('Basic details customers will see.')
                            ->icon('heroicon-o-information-circle')
                            ->columnSpan(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, ?string $state, string $operation) {
                                        if ($operation === 'create') {
                                            $set('slug', Str::slug($state));
                                        }
                                    }),
                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Used in the product URL. Auto-generated from the name.'),
                                Textarea::make('description')
                                    ->rows(6)
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Product image')
                            ->icon('heroicon-o-photo')
                            ->columnSpan(1)
                            ->schema([
                                FileUpload::make('img')
                                    ->label('Main image')
                                    ->image()
                                    ->imageEditor()
                                    ->disk('public')
                                    ->directory('products')
                                    ->maxSize(2048)
                                    ->hiddenLabel(),
                            ]),
                    ]),

                Section::make('Variants & pricing')
                    ->description('Add at least one variant (e.g. size, color). Prices are in Rs.')
                    ->icon('heroicon-o-currency-rupee')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('product_varients')
                            ->relationship()
                            ->hiddenLabel()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Variant name')
                                    ->placeholder('e.g. Large / Red')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('price')
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix('Rs.')
                                    ->required()
                                    ->live(onBlur: true),
                                TextInput::make('discount')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(fn (Get $get) => (float) ($get('price') ?: 0))
                                    ->prefix('Rs.')
                                    ->default(0)
                                    ->helperText('Flat amount off the price.'),
                                FileUpload::make('img')
                                    ->label('Variant image')
                                    ->image()
                                    ->disk('public')
                                    ->directory('products/variants')
                                    ->maxSize(2048)
                                    ->columnSpanFull(),
                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->minItems(1)
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                            ->addActionLabel('Add variant'),
                    ]),
            ]);
    }
}
