<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\CustomStatsWidget;
use App\Filament\Widgets\AnotherWidget;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard';
    protected static ?string $navigationGroup = 'Core';

    // Ubah level akses dari protected menjadi public
    public function getWidgets(): array
    {
        return [
            CustomStatsWidget::class, 
        ];
    }
}
