<?php

namespace App\Filament\Admin\Resources\Orders\Tables;

use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\URL;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Order #')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('user.email')
                    ->label('Customer email')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                TextColumn::make('contact_no')
                    ->label('Contact number')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                TextColumn::make('shipping_address')
                    ->label('Delivery address')
                    ->limit(40)
                    ->tooltip(fn (Order $record): ?string => $record->shipping_address)
                    ->wrap()
                    ->toggleable(),
                TextColumn::make('merchant.shop_name')
                    ->label('Shop')
                    ->searchable(),
                TextColumn::make('total_amount')
                    ->money('NPR')
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->label('Payment')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'khalti' => 'Khalti',
                        'cash_on_delivery', 'cod' => 'Cash on delivery',
                        default => ucfirst((string) $state),
                    }),
                TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'paid' ? 'success' : 'warning'),
                SelectColumn::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                    ])
                    ->selectablePlaceholder(false)
                    ->afterStateUpdated(function (Order $record, string $state): void {
                        Notification::make()
                            ->title('Order status updated to ' . ucfirst($state))
                            ->success()
                            ->send();
                    }),
                TextColumn::make('created_at')
                    ->label('Placed')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                    ]),
                SelectFilter::make('payment_status')
                    ->options([
                        'unpaid' => 'Unpaid',
                        'paid' => 'Paid',
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('approveOrder')
                        ->label('Approve order')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (Order $record): bool => $record->status === 'pending')
                        ->action(function (Order $record): void {
                            $record->update(['status' => 'approved']);

                            Notification::make()
                                ->title('Order approved')
                                ->body('The customer will see the updated status in order history.')
                                ->success()
                                ->send();
                        }),
                    Action::make('markPaid')
                        ->label('Mark paid')
                        ->icon('heroicon-o-banknotes')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (Order $record): bool => $record->payment_status !== 'paid')
                        ->action(function (Order $record): void {
                            $record->update(['payment_status' => 'paid']);

                            Notification::make()
                                ->title('Order marked as paid')
                                ->success()
                                ->send();
                        }),
                    Action::make('receipt')
                        ->label('Print receipt')
                        ->icon('heroicon-o-printer')
                        ->url(fn (Order $record): string => URL::temporarySignedRoute(
                            'admin.orders.receipt',
                            now()->addMinutes(30),
                            ['order' => $record->getKey()],
                        ))
                        ->openUrlInNewTab(),
                ])->button()->label('Manage'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
