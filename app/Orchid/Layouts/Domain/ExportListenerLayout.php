<?php

namespace App\Orchid\Layouts\Domain;

use App\Services\B24ExportService;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Listener;
use Orchid\Screen\Repository;
use Orchid\Support\Facades\Layout;

class ExportListenerLayout extends Listener
{
    protected $targets = [
        'crm_category_id'
    ];

//    private B24ExportService $service;
//
//    public function __construct(B24ExportService $service)
//    {
//        $this->service = $service;
//    }


    protected function layouts(): iterable
    {
        $request = request();
        return [
            Layout::rows([
                Select::make('crm_category_id')
                    ->options($this->query->get('crm_category_list') ?? [])
                    ->title('Категория')
                    ->value($request->post('crm_category_id'))
                    ->horizontal(),

                Select::make('stage_id')
                    ->options($this->query->get('crm_stage_list') ?? [])
                    ->title('Стадия сделки')
                    ->value($request->post('stage_id'))
                    ->horizontal(),

                Select::make('assigned_by_id')
                    ->options($this->query->get('crm_user_list') ?? [])
                    ->title('Ответственный')
                    ->value($request->post('assigned_by_id'))
                    ->horizontal()
            ])
        ];
    }

    public function handle(Repository $repository, Request $request): Repository
    {
        // TODO: Переделать через DI
        $webhook = $request->user()->getBitrix24Webhook();
        $this->service = new B24ExportService($webhook);

        $categoryId = $request->post('crm_category_id');

        $categoryList = $this->service->getDealCategoryList();
        $stageList = $this->service->getDealStageList($categoryId);
        $userList = $this->service->getUserList();

        return $repository
            ->set('crm_category_list', $categoryList)
            ->set('crm_stage_list', $stageList)
            ->set('crm_user_list', $userList);
    }
}
