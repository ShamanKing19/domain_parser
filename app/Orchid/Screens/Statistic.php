<?php

namespace App\Orchid\Screens;

use Illuminate\Support\Facades\DB;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class Statistic extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        ini_set('max_execution_time', 300); //5 минут
        $searchStatisticCMS = cache()->remember('statisticCMS', 60*60*24, function(){
            return DB::table('domain_info')
                ->select('cms', DB::raw('count(*) as total'))
                ->where('cms','!=','')
                ->groupBy('cms')
                ->orderBy('total', 'DESC')
                ->get();
        });
        $searchStatisticStatus = cache()->remember('statisticStatus', 60*60*24, function(){
            return DB::table('domains')
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->orderBy('total', 'DESC')
                ->get();
        });
        return [
            'searchStatisticCMS' => $searchStatisticCMS,
            'searchStatisticStatus' => $searchStatisticStatus,
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Статистика';
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::view('statistic'),
        ];
    }
}
