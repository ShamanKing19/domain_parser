<?php

namespace App\Orchid\Screens\ProcessingStatus;

use App\Models\ProcessingStatus;
use App\Orchid\Layouts\ProcessingStatus\ProcessingStatusLayout;
use Exception;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Toast;

class ProcessingStatusScreen extends Screen
{
    public function query(ProcessingStatus $status): iterable
    {
        $this->status = $status;

        return [
            'status' => $this->status
        ];
    }

    public function name(): ?string
    {
        return $this->status->name ?? 'Создание статуса';
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Назад')
                ->icon('bs.arrow-bar-left')
                ->class('btn btn-link mr-10')
                ->route('platform.processing-statuses.list'),

            Button::make('Удалить')
                ->icon('x-circle')
                ->confirm('Вы точно хотите удалить тип?')
                ->method('delete')
                ->canSee($this->status->exists),

            Button::make('Сохранить')
                ->icon('check-circle')
                ->method('save')
        ];
    }

    public function save(Request $request, ProcessingStatus $status)
    {
        $fields = $request->all();

        try {
            $success = $status->fill($fields)->save();
            if ($status->wasRecentlyCreated) {
                Toast::success('Запись была создана!');

                return redirect()->route('platform.processing-statuses.detail', ['status' => $status->id]);
            }

            if ($success) {
                Alert::success('Данные успешно обновлены!' . $status->wasRecentlyCreated);

                return;
            }

            Alert::error('Что-то пошло не так при обновлении...');

        } catch (Exception $e) {
            Alert::error($e->getMessage());
        }
    }

    public function delete(ProcessingStatus $status)
    {
        $success = $status->delete();
        if ($success) {
            Toast::success('Запись успешно удалена!');

            return redirect()->route('platform.processing-statuses.list');
        }

        Alert::error('Что-то пошло не так при удалении...');
    }

    public function layout(): iterable
    {
        return [
            ProcessingStatusLayout::class
        ];
    }
}
