<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditDomainRequest;
use App\Http\Requests\StoreDomainRequest;
use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Date;

class DomainController extends Controller
{

    public function index(Request $request)
    {
        return [];
    }

    public function view(Domain $domain)
    {
        return $domain;
    }

    public function viewByCms(string $cms)
    {
        return $cms;
    }

    /**
     * Создание записи с доменом
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
}
