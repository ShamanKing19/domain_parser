<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Illuminate\Support\Facades\DB;

class INN extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        if(isset($_GET['inn'])){
            $searchText = $_GET['inn'];
            $searchINN = DB::table('inns')->where('inns.inn','=',$searchText)->leftjoin('company_info',
                'company_info.inn_id', '=', 'inns.id')
                ->leftjoin('company_finances',
                    'company_finances.inn_id', '=', 'inns.id')
                ->where('company_finances.year', '=', '2020')
                ->get([
                    'company_info.name',
                    'company_info.type',
                    'company_info.address',
                    'company_info.region',
                    'company_info.main_activity',
                    'company_finances.income',
                    'company_finances.outcome',
                    'company_finances.profit',
                    'company_finances.year',
                    'company_info.boss_name',
                ]);
        }
        else{
            $searchText = '7716792416';
            $searchINN = DB::table('inns')->where('inns.inn','=',$searchText)->leftjoin('company_info',
                'company_info.inn_id', '=', 'inns.id')
                ->leftjoin('company_finances',
                    'company_finances.inn_id', '=', 'inns.id')
                ->where('company_finances.year', '=', '2020')
                ->get([
                    'company_info.name',
                    'company_info.type',
                    'company_info.address',
                    'company_info.region',
                    'company_info.main_activity',
                    'company_finances.income',
                    'company_finances.outcome',
                    'company_finances.profit',
                    'company_finances.year',
                    'company_info.boss_name',
                ]);
        }
        return [
            'searchINN' => $searchINN,
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'ИНН';
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
        ];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::view('inn'),
        ];
    }
}
