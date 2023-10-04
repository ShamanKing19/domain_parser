<?php
namespace App\Repositories;

use App\Models\Domain;
use Illuminate\Support\Collection;

class DomainRepository
{

    /**
     * Получение записи по id
     *
     * @param int $id
     *
     * @return Domain|null
     */
    public function getById(int $id) : Domain|null
    {
        return Domain::find($id);
    }

    /**
     * Получение списка доменов по списку id
     *
     * @param array $idList
     *
     * @return array
     */
    public function getListById(array $idList) : array
    {
        return Domain::whereIn('id', $idList)->select('domain')->pluck('domain')->toArray();
    }

    /**
     * @param string $domain
     *
     * @return Domain|null
     */
    public function getByDomain(string $domain) : Domain|null
    {
        return Domain::where('domain', '=', $domain)->first();
    }

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
        return Domain::paginate($itemsPerPage, ['id', 'domain'])->getCollection();
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
        return Domain::where('cms', '=', $cms)->paginate($itemsPerPage)->getCollection();
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
        return $count === 0 || $count < 0 ? Domain::getModel()->getPerPage() : $count;
    }

    /**
     * Получение всех существующих CMS
     *
     * @return array
     */
    public function getCmsList() : array
    {
        return Domain::select('cms')->groupBy('cms')->pluck('cms')->filter()->toArray();
    }

    /**
     * Получение всех существующих статусов ответа
     *
     * @return array
     */
    public function getStatusList() : array
    {
        return Domain::select('status')->groupBy('status')->pluck('status')->filter()->toArray();
    }

    /**
     * Получение id всех типов
     *
     * @return array
     */
    public function getTypeList() : array
    {
        return Domain::select('type_id')->groupBy('type_id')->pluck('type_id')->filter()->toArray();
    }
}
