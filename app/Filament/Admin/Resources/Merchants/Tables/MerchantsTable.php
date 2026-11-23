<?php

namespace App\Filament\Admin\Resources\Merchants\Tables;

use App\Mail\MerchantApproved;
use App\Models\Merchant;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class MerchantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Owner')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('shop_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('contact'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'approve' => 'success',
                        'reject' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('created_at')
                    ->label('Applied')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('approve')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('This approves the shop, creates a temporary password, and emails the sign-in details to the merchant.')
                    ->visible(fn (Merchant $record): bool => $record->status === 'pending')
                    ->action(function (Merchant $record): void {
                        $temporaryPassword = Str::random(16);

                        $record->update([
                            'status' => 'approve',
                            'password' => $temporaryPassword,
                        ]);

                        try {
                            Mail::to($record->email)->send(new MerchantApproved($record, $temporaryPassword));

                            Notification::make()
                                ->title('Merchant approved')
                                ->body('The merchant received their login credentials by email.')
                                ->success()
                                ->send();
                        } catch (Throwable $exception) {
                            $record->update([
                                'status' => 'pending',
                                'password' => null,
                            ]);

                            Log::error('Merchant approval email failed: ' . $exception->getMessage(), [
                                'merchant_id' => $record->id,
                            ]);

                            Notification::make()
                                ->title('Approval email could not be sent')
                                ->body('The application remains pending. Check the mail configuration and try again.')
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('reject')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Merchant $record): bool => $record->status === 'pending')
                    ->action(fn (Merchant $record) => $record->update(['status' => 'reject'])),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
