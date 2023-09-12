<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditDomainRequest;
use App\Http\Requests\EditManyDomainRequest;
use App\Http\Requests\StoreDomainRequest;
use App\Models\Domain;
use App\Services\DomainService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Date;

class DomainController extends Controller
{
    private DomainService $service;

    public function __construct(DomainService $service)
    {
        $this->service = $service;
    }

    /**
     * Получение списка доменов
     * // TODO: Вынести в репозиторий
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $itemsPerPage = $request->get('count') ?: \App\Models\Domain::getModel()->getPerPage();
        $domains = \App\Models\Domain::paginate($itemsPerPage, ['id', 'domain']);

        return \Response::success('', $domains);
    }

    /**
     * Детальная информация о домене
     *
     * @param Domain $domain
     *
     * @return Response
     */
    public function view(Domain $domain): Response
    {
        return \Response::success('' , $domain->load(['phones', 'emails', 'inns']));
    }

    /**
     * Список доменов отфильтрованный по CMS
     * // TODO: Вынести в репозиторий
     *
     * @param Request $request
     * @param string $cms Название CMS
     *
     * @return Response
     */
    public function viewByCms(Request $request, string $cms): Response
    {
        $itemsPerPage = $request->get('count') ?: \App\Models\Domain::getModel()->getPerPage();
        $domains = \App\Models\Domain::where('cms', '=', $cms)->paginate($itemsPerPage);

        return \Response::success('', $domains);
    }

    /**
     * Создание записи с доменом
     *
     * @param StoreDomainRequest $request
     *
     * @return Response
     */
    public function store(StoreDomainRequest $request): Response
    {
        $fields = $request->validated();
        $fields['updated_at'] = Date::now();

        $domain = $this->service->create($fields);

        return \Response::success('Запись создана!', ['id' => $domain->id]);
    }

    /**
     * Создание сразу нескольких записей (для миграции доменов)
     * // TODO: Добавить валидацию как в editMany
     *
     * @param Request $request
     *
     * @return Response
     */
    public function storeMany(Request $request): Response
    {
        $fields = $request->all();
        $success = $this->service->createMany($fields);
        if(!$success) {
            return \Response::error('Что-то пошло не так при создании записей');
        }

        return \Response::success('Записи созданы!');
    }

    /**
     * Изменение полей домена
     *
     * @param EditDomainRequest $request
     *
     * @return Response
     */
    public function edit(EditDomainRequest $request): Response
    {
        $fields = $request->validated();
        $fields['updated_at'] = Date::now();

        $domain = $this->service->update($fields['id'], $fields);
        if(!$domain->wasChanged()) {
            return \Response::error('Что-то пошло не так при обновлении');
        }

        return \Response::success('Запись обновлена!', [$domain->getChanges()]);
    }

    /**
     * Изменение полей домена
     *
     * @param EditManyDomainRequest $request
     *
     * @return Response
     */
    public function editMany(EditManyDomainRequest $request): Response
    {
        $allFields = $request->validated();

        $changedFields = [];
        foreach($allFields['domains'] as $fields) {
            $domainId = $fields['id'];
            $domain = $this->service->update($domainId, $fields);
            $changedFields[$domainId] = $domain->getChanges();
        }

        return \Response::success('Записи обновлены!', $changedFields);
    }
}
