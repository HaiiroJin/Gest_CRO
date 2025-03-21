<?php

namespace App\Filament\UserPages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public static function getNavigationLabel(): string
    {
        return 'Tableau de Bord';
    }

    public function getTitle(): string
    {
        return 'Tableau de Bord Fonctionnaire';
    }
}
