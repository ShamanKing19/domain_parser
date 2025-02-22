<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditDomainRequest;
use App\Http\Requests\EditManyDomainRequest;
use App\Http\Requests\StoreDomainRequest;
use App\Models\Domain;
use App\Repositories\DomainRepository;
use App\Services\DomainService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Date;

class DomainController extends Controller
{
    private DomainService $service;

    private DomainRepository $repository;

    public function __construct(DomainService $service, DomainRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    /**
     * Получение списка доменов
     * // TODO: Вынести в репозиторий
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request): Response
    {
        $itemsPerPage = $request->get('count') ?: Domain::getModel()->getPerPage();
        $domains = Domain::paginate($itemsPerPage, ['id', 'domain']);

        return \Response::success('', $domains);
    }

    /**
     * Получение записей с учётом фильтров в GET запросе
     *
     * @param Request $request
     *
     * @return Response
     */
    public function get(Request $request): Response
    {
        $filters = $request->all();
        $domains = Domain::with(['phones', 'emails'])->where($filters)->get();

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
        return \Response::success('', $domain->load(['phones', 'emails', 'inns']));
    }

    /**
     * Список доменов отфильтрованный по CMS
     *
     * @param Request $request
     * @param string $cms Название CMS
     *
     * @return Response
     */
    public function viewByCms(Request $request, string $cms): Response
    {
        $itemsPerPage = $request->get('count') ?: 0;
        $domains = $this->repository->getDomainsByCms($cms, $itemsPerPage);

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
        if (!$success) {
            return \Response::error('Что-то пошло не так при создании записей');
        }

        return \Response::success('Записи созданы!');
    }

    /**
     * Изменение полей домена или его создание
     *
     * @param EditDomainRequest $request
     *
     * @return Response
     */
    public function edit(EditDomainRequest $request): Response
    {
        $fields = $request->validated();
        $fields['updated_at'] = Date::now();

        $domain = $this->service->createOrUpdate($fields);

        if (!$domain) {
            return \Response::error('Что-то пошло не так...');
        }

        if (!$domain->wasRecentlyCreated && !$domain->wasChanged()) {
            return \Response::success('Ничего не было обновлено');
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
        foreach ($allFields['domains'] as $fields) {
            $domain = $this->service->createOrUpdate($fields);
            if ($domain) {
                $changedFields[$domain->id] = $domain->getChanges();
            }
        }

        return \Response::success('Записи обновлены!', $changedFields);
    }
}
