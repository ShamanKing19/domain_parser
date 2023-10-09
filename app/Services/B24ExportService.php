<?php
namespace App\Services;
use App\Models\Domain;
use Illuminate\Support\Facades\Http;

class B24ExportService
{
    /** @var string Вебхук bitrix24 */
    private string $baseUrl;

    public function __construct(string $webhook)
    {
        $this->baseUrl = $webhook;
    }

    /**
     * Получение списка пользователей
     *
     * @return array
     */
    public function getUserList() : array
    {
        $userList = $this->makeRequest('user.get');
        if(!$userList) {
            return [];
        }

        $userItems = [];
        foreach($userList as $user) {
            $fullName = implode(' ', array_filter([$user['LAST_NAME'], $user['NAME']]));
            $userItems[$user['ID']] = $fullName;
        }

        return $userItems;
    }

    /**
     * Получение категорий
     *
     * @return array
     */
    public function getDealCategoryList() : array
    {
        $categoryList =  $this->makeRequest('crm.dealcategory.list');
        if(!$categoryList) {
            return [];
        }

        $categoryItems = [];
        foreach($categoryList as $category) {
            $categoryItems[$category['ID']] = $category['NAME'];
        }

        return $categoryItems;
    }

    /**
     * Получение стадий сделки
     *
     * @return array
     */
    public function getDealStageList(int $categoryId = 0) : array
    {
        $stageList = $this->makeRequest('crm.dealcategory.stage.list', [
            'ID' => $categoryId
        ]);

        if(!$stageList) {
            return [];
        }

        $stageItems = [];
        foreach($stageList as $stage) {
            $stageItems[$stage['STATUS_ID']] = $stage['NAME'];
        }

        return $stageItems;
    }

    /**
     * Создание сделки в crm
     *
     * @param Domain $domain
     * @param int $categoryId Категория (воронка)
     * @param int $assignedById Ответственный
     * @param int $contactId ID контакта
     *
     * @return int
     */
    public function createDeal(Domain $domain, int $categoryId, string $stageId, int $assignedById, int $contactId = 0) : int
    {
        $fields = [
            'TITLE' => $domain->domain,
            'CATEGORY_ID' => $categoryId,
            'STAGE_ID' => $stageId,
            'ASSIGNED_BY_ID' => $assignedById
        ];

        if($contactId) {
            $fields['CONTACT_ID'] = $contactId;
        }

        return $this->makeRequest('crm.deal.add', [
            'FIELDS' => $fields
        ]) ?: 0;
    }

    /**
     * Создание контакта в crm
     *
     * @param Domain $domain
     * @param int $assignedById Ответственный
     *
     * @return int
     */
    public function createContact(Domain $domain, int $assignedById = 0) : int
    {
        $emailList = $domain->emails->pluck('email');
        $phoneList = $domain->phones->pluck('phone');
        if($emailList->isEmpty() && $phoneList->isEmpty()) {
            return 0;
        }

        $emails = [];
        foreach($emailList as $email) {
            $emails[] = [
                'VALUE' => $email,
                'VALUE_TYPE' => 'WORK'
            ];
        }

        $phones = [];
        foreach($phoneList as $phone) {
            $phones[] = [
                'VALUE' => $phone,
                'VALUE_TYPE' => 'WORK'
            ];
        }

        return $this->makeRequest('crm.contact.add', [
            'FIELDS' => [
                'NAME' => $domain->domain,
                'LAST_NAME' => '',
                'EMAIL' => $emails,
                'PHONE' => $phones,
                'ASSIGNED_BY_ID' => $assignedById
            ]
        ]) ?: 0;
    }

    /**
     * Запрос к crm
     *
     * @param string $methodName Название rest метода bitrix24
     * @param array $params
     *
     * @return array|int|null
     */
    private function makeRequest(string $methodName, array $params = [])
    {
        try {
            $request = Http::get($this->baseUrl . '/' . $methodName, $params);
            $response = $request->json();
            if($response) {
                return $response['result'];
            }

            return [];
        } catch (\Exception $e) {
            return null;
        }
    }

}
