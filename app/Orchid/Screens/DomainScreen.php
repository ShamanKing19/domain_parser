<?php

namespace App\Orchid\Screens;

use App\Models\Domain;
use App\Orchid\Layouts\Company\CompanyFinancesLayout;
use App\Orchid\Layouts\Company\CompanyLayout;
use App\Orchid\Layouts\Domain\DomainContactsLayout;
use App\Orchid\Layouts\Domain\DomainLayout;
use Illuminate\Support\Collection;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class DomainScreen extends Screen
{
    private mixed $name;

    private Domain $domain;
    private Collection $companies;


    public function query(Domain $domain): iterable
    {
        $this->domain = $domain;
        $this->name = $this->domain->domain;
        $this->companies = $this->domain->companies()->with('financeYears')->get();

        $result = [
            'domain' => $this->domain
        ];

        foreach($this->companies as $company) {
            $result['company_' . $company->inn] = $company->financeYears()->paginate();
        }

        return $result;
    }

    public function name(): ?string
    {
        return $this->domain->domain;
    }

    public function description() : ?string
    {
       return $this->domain->title;
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Назад')
                ->route('platform.domains.list')
                ->icon('bs.arrow-bar-left')
                ->class('btn btn-link mr-10'),

            // TODO: Запускать парсер и обновлять страницу
            Button::make('Обновить данные')
                ->icon('bs.arrow-clockwise')
                ->method('update'),

            // TODO: Implement
//            Button::make('Удалить')
//                ->icon('trash')
//                ->method('remove')
//                ->confirm('Запись удалится из базы данных'),
        ];
    }

    public function layout(): iterable
    {
        $tabs = [
            'Общая информация' => new DomainLayout(),
            'Контакты' => new DomainContactsLayout(),
        ];

        if($this->companies->isNotEmpty()) {
            foreach($this->companies as $company) {
                $tab = [new CompanyLayout($company)];
                if($company->financeYears()->exists()) {
                    $tab[] = new CompanyFinancesLayout($company);
                }

                $tabs['ИНН: ' . $company->inn] = $tab;
            }
        }

        return [
            Layout::tabs($tabs)
        ];
    }

    public function update(Domain $domain)
    {
        exec('~/.nvm/versions/node/v16.17.1/bin/node /home/production/web/domainsparse.dev.skillline.ru/public_html/parser.js --domain=' . $domain->domain, $result, $errorCode);
        if($errorCode === 0) {
            Alert::success('Данные успешно обновлены!');
            return;
        }

        $response = implode('', $result);
        $response = json_decode($response, true);
        Alert::withoutEscaping()->error('<pre style="background-color: rgba(0, 0, 0, 0);">'.json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).'</pre>');
    }
}
