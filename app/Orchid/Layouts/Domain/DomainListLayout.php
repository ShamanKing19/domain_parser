<?php

namespace App\Orchid\Layouts\Domain;

use App\Helpers;
use App\Models\Domain;
use App\Models\ProcessingStatus;
use App\Models\WebsiteType;
use App\Repositories\DomainRepository;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class DomainListLayout extends Table
{
    protected $target = 'domains';
    private array $cmsList = [];
    private array $statusList = [];
    private array $typeList = [];
    private array $processingStatusList = [];

    public function __construct(DomainRepository $repository)
    {
        $cmsList = $repository->getCmsList();
        foreach ($cmsList as $cms) {
            $this->cmsList[$cms] = $cms;
        }

        $statusList = $repository->getStatusList();
        foreach ($statusList as $status) {
            $this->statusList[$status] = $status;
        }

        $typeIdList = $repository->getTypeList();
        $typeList = WebsiteType::where('id', '=', $typeIdList)->get();
        foreach ($typeList as $type) {
            $this->typeList[$type->id] = $type->name;
        }

        $processingStatusList = ProcessingStatus::all();
        foreach ($processingStatusList as $status) {
            $this->processingStatusList[$status->id] = $status->name;
        }
    }

    protected function columns(): iterable
    {
        return [
            TD::make('checkbox', '#')->render(fn(Domain $domain) => CheckBox::make('domain_id_list[]')->value($domain->id)->checked(false)),

            TD::make('id')
                ->render(function (Domain $domain) {
                    return Link::make($domain->id)->route('platform.domains.detail', ['domain' => $domain->id]);
                })
                ->filter(Input::make())
                ->sort(),

            TD::make('domain', 'Домен')
                ->render(function (Domain $domain) {
                    return Link::make($domain->domain)->route('platform.domains.detail', ['domain' => $domain->id]);
                })
                ->filter(Input::make())
                ->sort(),

            TD::make('real_domain', 'Конечная ссылка')
                ->render(function (Domain $domain) {
                    if (!empty($domain->real_domain)) {
                        return Link::make($domain->real_domain)->href($domain->real_domain ?? '')->target('blank');
                    }
                })
                ->filter(Input::make())
                ->sort(),

            TD::make('processing_status_id', 'Статус')
                ->render(function (Domain $domain) {
                    return $domain->processingStatus->name ?? '';
                })
                ->filter(TD::FILTER_SELECT, $this->processingStatusList)
                ->sort(),

            TD::make('status', 'HTTP Статус')->filter(TD::FILTER_SELECT, $this->statusList)->sort(),

            TD::make('cms', 'CMS')
                ->filter(TD::FILTER_SELECT, $this->cmsList)
                ->sort(),

            TD::make('type_id', 'Тип')
                ->render(function (Domain $domain) {
                    return $domain->type->name ?? '';
                })
                ->filter(TD::FILTER_SELECT, $this->typeList)
                ->sort(),

            TD::make('title', 'Заголовок')
                ->render(function (Domain $domain) {
                    return $domain->title ? Helpers::truncate($domain->title, 60, '...') : '';
                })
                ->filter(Input::make())
                ->sort(),

            TD::make('description', 'Описание')
                ->render(function (Domain $domain) {
                    return $domain->description ? Helpers::truncate($domain->description, 60, '...') : '';
                })
                ->filter(Input::make())
                ->sort()
                ->defaultHidden(),

            TD::make('keywords', 'Ключевые слова')
                ->render(function (Domain $domain) {
                    return $domain->keywords ? Helpers::truncate($domain->keywords, 60, '...') : '';
                })
                ->filter(Input::make())
                ->sort()
                ->defaultHidden(),

            TD::make('has_ssl', 'SSL')
                ->filter(Input::make())
                ->sort(),

            TD::make('has_https_redirect', 'HTTPS редирект')
                ->filter(Input::make())
                ->sort(),

            TD::make('has_catalog', 'Каталог')
                ->filter(Input::make())
                ->sort(),

            TD::make('has_basket', 'Корзина')
                ->filter(Input::make())
                ->sort(),

            TD::make('income', 'Выручка')
                ->sort(),

            TD::make('emails_string', 'Почты')
                ->render(function (Domain $domain) {
                    return $domain->emails_string ? Helpers::truncate($domain->emails_string, 60, '...') : '';
                })
        ];
    }
}
