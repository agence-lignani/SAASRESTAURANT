<?php

namespace App\Filament\Pages;

use App\Models\Restaurant;
use App\Support\Analytics\SiteTrafficReport;
use App\Support\Filament\FilamentAccess;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Panel;
use UnitEnum;

class ManageSiteTrafficPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected string $view = 'filament.pages.manage-site-traffic';

    protected static ?string $navigationLabel = 'Trafic site';

    protected static ?string $title = 'Statistiques du site public';

    protected ?string $subheading = 'Visites enregistrées sur les pages principales (GET). Hors API technique (créneaux, etc.).';

    protected static ?int $navigationSort = 52;

    protected static string|UnitEnum|null $navigationGroup = 'Site vitrine';

    public int $days = 7;

    /** @var array<string, mixed> */
    public array $report = [];

    public static function canAccess(): bool
    {
        return FilamentAccess::canManageBookings();
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return 'trafic-site';
    }

    public function mount(): void
    {
        $this->loadReport();
    }

    public function setDays(int $days): void
    {
        if (! in_array($days, [7, 30, 90], true)) {
            return;
        }
        if ($this->days === $days) {
            return;
        }
        $this->days = $days;
        $this->loadReport();
    }

    private function loadReport(): void
    {
        /** @var Restaurant $restaurant */
        $restaurant = app('filament.restaurant');
        $this->report = SiteTrafficReport::build($restaurant, $this->days);
    }
}
