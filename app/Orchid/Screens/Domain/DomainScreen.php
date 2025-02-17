<?php

namespace App\Orchid\Screens\Domain;

use App\Models\Domain;
use App\Orchid\Layouts\Company\CompanyFinancesLayout;
use App\Orchid\Layouts\Company\CompanyLayout;
use App\Orchid\Layouts\Domain\DomainContactsLayout;
use App\Orchid\Layouts\Domain\DomainLayout;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

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

        foreach ($this->companies as $company) {
            $result['company_' . $company->inn] = $company->financeYears()->paginate();
        }

        return $result;
    }

    public function name(): ?string
    {
        return $this->domain->domain;
    }

    public function description(): ?string
    {
        return $this->domain->title;
    }

    public function commandBar(): iterable
    {
        /* Кнопка "Назад" */
        $previousUrl = url()->previous();
        $backButton = Link::make('Назад')
            ->icon('bs.arrow-bar-left')
            ->class('btn btn-link mr-10');

        if ($previousUrl && $previousUrl !== url()->current()) {
            $backButton->href($previousUrl);
        } else {
            $backButton->route('platform.domains.list');
        }

        return [
            $backButton,

            Button::make('Обновить данные')
                ->icon('bs.arrow-clockwise')
                ->method('update'),

            Button::make('Удалить')
                ->icon('x-circle')
                ->confirm('Вы точно хотите удалить домен?')
                ->method('delete')
                ->canSee($this->domain->exists),

            Button::make('Сохранить')
                ->icon('bs.check-circle')
                ->method('save'),

            Link::make('Перейти на сайт')
                ->href($this->domain->real_domain ?? '')
                ->target('_blank')
                ->icon('bs.box-arrow-up-left')
                ->canSee(!!$this->domain->real_domain)
        ];
    }

    public function update(Domain $domain): void
    {
        $nodePath = config('parser.node_path');
        $parserPath = config('parser.parser_path');
        exec("$nodePath $parserPath --domain=$domain->domain", $result, $errorCode);
        if ($errorCode === 0) {
            Alert::success('Данные успешно обновлены!');

            return;
        }

        $response = implode('', $result);
        $response = json_decode($response, true);
        Alert::withoutEscaping()->error('<pre style="background-color: rgba(0, 0, 0, 0);">' . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</pre>');
    }

    public function save(Request $request, Domain $domain)
    {
        $fields = $request->post('domain');
        if (isset($fields['type_id'])) {
            $fields['auto_type_id'] = $fields['type_id'];
        }

        $result = $domain->fill($fields)->save();
        if (!$result) {
            Alert::error('Что-то пошло не так при обновлении...');

            return;
        }

        Alert::success('Данные успешно обновлены!');
    }

    public function delete(Domain $domain)
    {
        $success = $domain->delete();
        if ($success) {
            Toast::success('Запись успешно удалена!');

            return redirect()->route('platform.domains.list');
        }

        Alert::error('Что-то пошло не так при удалении...');
    }

    public function layout(): iterable
    {
        $tabs = [
            'Общая информация' => new DomainLayout()
        ];

        if ($this->domain->emails()->exists() || $this->domain->phones()->exists()) {
            $tabs['Контакты'] = new DomainContactsLayout();
        }

        if ($this->companies->isNotEmpty()) {
            foreach ($this->companies as $company) {
                $tab = [new CompanyLayout($company)];
                if ($company->financeYears()->exists()) {
                    $tab[] = new CompanyFinancesLayout($company);
                }

                $tabs['ИНН: ' . $company->inn] = $tab;
            }
        }

        return [
            Layout::tabs($tabs)
        ];
    }
}
