<?php

namespace App\Orchid\Screens;

use App\Models\WebsiteType;
use App\Orchid\Layouts\WebsiteType\WebsiteTypeLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Toast;

class WebsiteTypeScreen extends Screen
{
    public function query(WebsiteType $type): iterable
    {
        $this->type = $type;
        return [
            'type' => $this->type
        ];
    }

    public function name(): ?string
    {
        return $this->type->name ?? 'Создание типа';
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Назад')
                ->icon('bs.arrow-bar-left')
                ->class('btn btn-link mr-10')
                ->route('platform.website-types.list'),

            Button::make('Удалить')
                ->icon('x-circle')
                ->confirm('Вы точно хотите удалить тип?')
                ->method('delete')
                ->canSee($this->type->exists),

            Button::make('Сохранить')
                ->icon('check-circle')
                ->method('save')
        ];
    }

    public function save(Request $request, WebsiteType $type)
    {
        $fields = $request->all();

        try {
            $success = $type->fill($fields)->save();
            if($type->wasRecentlyCreated) {
                Toast::success('Запись была создана!');
                return redirect()->route('platform.website-types.detail', ['type' => $type->id]);
            }

            if($success) {
                Alert::success('Данные успешно обновлены!' . $type->wasRecentlyCreated);
                return;
            }

            Alert::error('Что-то пошло не так при обновлении...');
        } catch (\Exception $e) {
            Alert::error($e->getMessage());
        }
    }

    public function delete(WebsiteType $type)
    {
        $success = $type->delete();
        if($success) {
            Toast::success('Запись успешно удалена!');
            return redirect()->route('platform.website-types.list');
        }

        Alert::error('Что-то пошло не так при удалении...');
    }

    public function layout(): iterable
    {
        return [
            WebsiteTypeLayout::class
        ];
    }
}
