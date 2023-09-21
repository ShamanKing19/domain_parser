<?php
namespace App\Repositories;

use Illuminate\Support\Collection;

class DomainRepository
{

    /**
     * Получение списка доменов
     *
     * @param int $count
     *
     * @return Collection
     */
    public function getDomains(int $count) : Collection
    {
        $itemsPerPage = $this->getItemsPerPage($count);
        return \App\Models\Domain::paginate($itemsPerPage, ['id', 'domain'])->getCollection();
    }

    /**
     * Получение списка доменов отфильтрованных по cms
     *
     * @param string $cms
     * @param int $count
     *
     * @return Collection
     */
    public function getDomainsByCms(string $cms, int $count) : Collection
    {
        $itemsPerPage = $this->getItemsPerPage($count);
        return \App\Models\Domain::where('cms', '=', $cms)->paginate($itemsPerPage)->getCollection();
    }

    /**
     * Получение количество элементов на странице
     *
     * @param int $count
     *
     * @return int
     */
    private function getItemsPerPage(int $count) : int
    {
        return $count === 0 || $count < 0 ? \App\Models\Domain::getModel()->getPerPage() : $count;
    }

    /**
     * Получение всех существующих CMS
     *
     * @return array
     */
    public function getCmsList() : array
    {
        return \App\Models\Domain::select('cms')->groupBy('cms')->pluck('cms')->filter()->toArray();
    }

    /**
     * Получение всех существующих статусов ответа
     *
     * @return array
     */
    public function getStatusList() : array
    {
        return \App\Models\Domain::select('status')->groupBy('status')->pluck('status')->filter()->toArray();
    }
}
