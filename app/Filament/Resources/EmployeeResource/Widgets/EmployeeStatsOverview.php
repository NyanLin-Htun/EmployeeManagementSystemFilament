<?php

namespace App\Filament\Resources\EmployeeResource\Widgets;

use App\Models\Country;
use App\Models\Employee;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class EmployeeStatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $mm = Country::where('country_code','MM')->with('employees')->first();
        $uk = Country::where('country_code','UK')->with('employees')->first();
        $us = Country::where('country_code','US')->with('employees')->first();
        return [
            Card::make('All Employees', Employee::all()->count())
                ->description('32k increase')
                ->descriptionIcon('heroicon-s-trending-up')
                ->color('success'),
            Card::make($mm->name . ' Employees', $mm ? $mm->employees->count(): 0)
                // ->description('7% increase')
                // ->descriptionIcon('heroicon-s-trending-down')
                ->color('danger'),
            Card::make($uk->name . ' Employees', $uk ? $uk->employees->count(): 0)
                // ->description('3% increase')
                // ->descriptionIcon('heroicon-s-trending-up')
                ->color('warning'),
            Card::make($us->name . ' Employees', $us ? $us->employees->count(): 0)
                ->color('info')
        ];
    }
}
