<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditDomainRequest;
use App\Http\Requests\EditManyDomainRequest;
use App\Http\Requests\StoreDomainRequest;
use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Date;

class DomainController extends Controller
{

    /**
     * Получение списка доменов
     * // TODO: Вынести в репозиторий
     *
     * @return Response
     */
    public function index(Request $request)
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
    public function view(Domain $domain)
    {
        return \Response::success('' , $domain->load(['phones', 'emails', 'inns']));
    }

    /**
     * Список доменов отфильтрованный по CMS
     * // TODO: Вынести в репозиторий
     *
     * @param string $cms Название CMS
     *
     * @return Response
     */
    public function viewByCms(Request $request, string $cms)
    {
        $itemsPerPage = $request->get('count') ?: \App\Models\Domain::getModel()->getPerPage();
        $domains = \App\Models\Domain::where('cms', '=', $cms)->paginate($itemsPerPage);

        return \Response::success('', $domains);
    }

    /**
     * Создание записи с доменом
     * // TODO: Вынести копипасту в сервис
     *
     * @param StoreDomainRequest $request
     *
     * @return Response
     */
    public function store(StoreDomainRequest $request)
    {
        $fields = $request->validated();
        $fields['updated_at'] = Date::now();

        $domain = \App\Models\Domain::create($fields);

        if(isset($fields['phones'])) {
            $phoneRows = collect($fields['phones'])->map(fn($item) => ['phone' => $item]);
            $domain->phones()->createMany($phoneRows);
        }

        if(isset($fields['emails'])) {
            $emailRows = collect($fields['emails'])->map(fn($item) => ['email' => $item]);
            $domain->emails()->createMany($emailRows);
        }

        if(isset($fields['inn'])) {
            $innRows = collect($fields['inn'])->map(fn($item) => ['inn' => $item]);
            $domain->inns()->createMany($innRows);
        }

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
    public function storeMany(Request $request)
    {
        $fields = $request->all();
        $now = Date::now();
        foreach($fields as &$field) {
            $field['updated_at'] = $now;
        }

        \App\Models\Domain::insert($fields);

        return \Response::success('Запись создана!');
    }

    /**
     * Изменение полей домена
     * // TODO: Вынести копипасту в сервис
     *
     * @param EditDomainRequest $request
     *
     * @return Response
     */
    public function edit(EditDomainRequest $request)
    {
        $fields = $request->validated();
        $fields['updated_at'] = Date::now();

        $domain = \App\Models\Domain::find($request->post('id'));
        if(empty($domain)) {
            return \Response::error('Запись не найдена');
        }

        if(isset($fields['phones'])) {
            $domain->phones()->delete();
            $phoneRows = collect($fields['phones'])->map(fn($item) => ['phone' => $item]);
            $domain->phones()->createMany($phoneRows);
        }

        if(isset($fields['emails'])) {
            $domain->emails()->delete();
            $phoneRows = collect($fields['emails'])->map(fn($item) => ['emails' => $item]);
            $domain->emails()->createMany($phoneRows);
        }

        if(isset($fields['inn'])) {
            $domain->inns()->delete();
            $phoneRows = collect($fields['inn'])->map(fn($item) => ['inn' => $item]);
            $domain->inns()->createMany($phoneRows);
        }

        $success = $domain->update($fields);
        if(!$success) {
            return \Response::error('Что-то пошло не так при обновлении');
        }

        return \Response::success('Запись обновлена!', [$domain->getChanges()]);
    }

    /**
     * Изменение полей домена
     * // TODO: Вынести копипасту в сервис
     *
     * @param EditManyDomainRequest $request
     *
     * @return Response
     */
    public function editMany(EditManyDomainRequest $request)
    {
        $allFields = $request->validated();

        $changedFields = [];
        foreach($allFields['domains'] as $fields) {
            $domainId = $fields['id'];
            $domain = \App\Models\Domain::find($domainId);
            if(empty($domain)) {
                continue;
            }

            $fields['updated_at'] = Date::now();

            if(isset($fields['phones'])) {
                $domain->phones()->delete();
                $phoneRows = collect($fields['phones'])->map(fn($item) => ['phone' => $item]);
                $domain->phones()->createMany($phoneRows);
            }

            if(isset($fields['emails'])) {
                $domain->emails()->delete();
                $phoneRows = collect($fields['emails'])->map(fn($item) => ['emails' => $item]);
                $domain->emails()->createMany($phoneRows);
            }

            if(isset($fields['inn'])) {
                $domain->inns()->delete();
                $phoneRows = collect($fields['inn'])->map(fn($item) => ['inn' => $item]);
                $domain->inns()->createMany($phoneRows);
            }

            $success = $domain->update($fields);
            if($success) {
                $changedFields[$domainId] = $domain->getChanges();
            }
        }

        return \Response::success('Записи обновлены!', $changedFields);
    }
}
