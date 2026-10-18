<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Orders', Order::count())
                ->description('All orders received')
                ->icon('heroicon-o-shopping-bag')
                ->color('primary'),

            Stat::make(
                'Pending Orders',
                Order::where('order_status', 'pending')->count()
            )
                ->description('Awaiting confirmation')
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make(
                'Confirmed Orders',
                Order::where('order_status', 'confirmed')->count()
            )
                ->description('Orders approved')
                ->icon('heroicon-o-check-circle')
                ->color('info'),

            Stat::make(
                'Delivered Orders',
                Order::where('order_status', 'delivered')->count()
            )
                ->description('Successfully delivered')
                ->icon('heroicon-o-truck')
                ->color('success'),
        ];
    }
}