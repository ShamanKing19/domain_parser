<?php

namespace App\Orchid\Screens;

use App\Order;
use Illuminate\Support\Facades\DB;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Domains extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {


        ini_set('max_execution_time', 240); //4 минуты
        if(isset($_GET['searchDomain']) || isset($_GET['searchCMS']) || isset($_GET['searchTitle']) || isset($_GET['searchRegion'])){
            if(!isset($_GET['searchTitle'])){
                $searchTitle = '';
            }
            else{
                $searchTitle = $_GET['searchTitle'];
            }
            if(!isset($_GET['searchCMS'])){
                $searchCMS = '';
            }
            else{
                $searchCMS = $_GET['searchCMS'];
            }
            if(!isset($_GET['searchDomain'])){
                $searchDomain = '';
            }
            else{
                $searchDomain = $_GET['searchDomain'];
            }
            if(!isset($_GET['searchRegion'])){
                $searchRegion = '';
            }
            else{
                $searchRegion = $_GET['searchRegion'];
            }
            $searchParser = DB::table('domain_info')
                ->select('domain_info.*')
                ->Where('domain_info.title', 'LIKE', '%' . strip_tags($searchTitle) . '%')
                ->Where('domain_info.cms', 'LIKE', '%' . strip_tags($searchCMS) . '%')
                ->Where('domain_info.real_domain', 'LIKE', '%' . strip_tags($searchDomain) . '%')
                ->Where('domain_info.city', 'LIKE', '%' . strip_tags($searchRegion) . '%')
                ->paginate(10)
                ->appends(request()->query());
            return [
                'searchParser' => $searchParser,
            ];
        }
        else{
            $searchText= '';
            return [
            ];
        }
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Домены';
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
            Layout::view('domains'),
        ];
    }
}
