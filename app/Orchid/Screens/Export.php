<?php

namespace App\Orchid\Screens;

use Illuminate\Support\Facades\DB;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Illuminate\Support\Facades\Cache;

class Export extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        ini_set('max_execution_time', 240); //4 минуты
        $getConstructor = "domain_info.real_domain domain_info.id";
        if(isset($_GET['about_client']) || isset($_GET['about_site']) || isset($_GET['about_finance']) || isset($_GET['about_ur'])) {
            if (isset($_GET['about_client'])) {
                $aboutClient = $_GET['about_client'];
                //$getConstructor .= " " . DB::raw('email') . ' ' . DB::raw("number");
                //$getConstructor .= " " . DB::raw('group_concat(email) as email') . ' ' . DB::raw("group_concat(number) as number");
            } else {
                $aboutClient = '';
            }
            if (isset($_GET['about_site'])) {
                $aboutSite = $_GET['about_site'];
                $getConstructor .= " title description keywords domain_info.city hosting cms ip";
            } else {
                $aboutSite = '';
            }
            if (isset($_GET['about_finance'])) {
                $aboutFinance = $_GET['about_finance'];
                $getConstructor .= " year income outcome profit segment registry_category";
            } else {
                $aboutFinance = '';
            }
            if (isset($_GET['about_ur'])) {
                $aboutUr = $_GET['about_ur'];
                $getConstructor .= " name type region domain_info.inn post_index boss_name boss_post main_activity";
            } else {
                $aboutUr = '';
            }
        }
        if(isset($_GET['confirmExport'])) {
            if (isset($_GET['about_finance'])) {
                $exportDomains = DB::table('domain_info')->
                select(explode(' ', $getConstructor))->
                addSelect("domain")->distinct()->
                addselect(DB::raw("GROUP_CONCAT(DISTINCT email) as email"))->
                addselect(DB::raw("GROUP_CONCAT(DISTINCT number) as number"))->
                where('bitrix_id', 'IS NULL')->
                whereNotNull('email')->
                leftJoin('domains', 'domains.id', '=', 'domain_info.domain_id')->
                leftJoin('domain_emails', 'domain_emails.domain_id', '=', 'domain_info.domain_id')->
                leftJoin('domain_phones', 'domain_phones.domain_id', '=', 'domain_info.domain_id')->
                leftJoin('inns', 'inns.inn', '=', 'domain_info.inn')->
                leftJoin('company_finances', 'company_finances.inn_id', '=', 'inns.id')->
                leftJoin('company_info', 'company_info.inn_id', '=', 'inns.id')->
                orderBy("year", 'DESC')->
                groupBy(explode(' ', $getConstructor), "domain")->
                limit(10)->get();
            } else {
                $exportDomains = DB::table('domain_info')->
                select(explode(' ', $getConstructor))->
                addSelect("domain")->distinct()->
                addselect(DB::raw("GROUP_CONCAT(DISTINCT email) as email"))->
                addselect(DB::raw("GROUP_CONCAT(DISTINCT number) as number"))->
                where('bitrix_id', 'IS NULL')->
                whereNotNull('email')->
                leftJoin('domains', 'domains.id', '=', 'domain_info.domain_id')->
                leftJoin('domain_emails', 'domain_emails.domain_id', '=', 'domain_info.domain_id')->
                leftJoin('domain_phones', 'domain_phones.domain_id', '=', 'domain_info.domain_id')->
                groupBy(explode(' ', $getConstructor), "domain")->
                limit(10)->get();
            }
            //CURL
            foreach ($exportDomains as $row) {
                $data = array(
                    "fields" => array(
                        "TITLE" => $row->domain,
                        "UF_CRM_1656402341" => $row->real_domain,
                        "ASSIGNED_BY_ID" => 10,
                        "CREATED_BY_ID" => 10
                    )
                );
                if (isset($_GET['about_client'])) {
                    $dataclient = array(
                        "fields" => array(
                            "UF_CRM_1655722376" => $row->email, // Email
                            "UF_CRM_1655723513" => $row->number, // Phone
                        )
                    );
                    $data = array_merge_recursive($data, $dataclient);
                }
                if (isset($_GET['about_finance'])){
                    $numberSegment = $row->segment;
//                    $numberRegistryCategory = $row->registry_category;
                    if ($numberSegment != '') {
                        $numberSegment += 36;
                    }
//                    if ($numberRegistryCategory != '') {
//                        $numberRegistryCategory += 58;
//                    }
                    $datafinance = array(
                        "fields" => array(
                            "UF_CRM_1656332862" => array(
                                "ID" => $numberSegment
                            ),
                            "UF_CRM_1656516854" => $row->income,
                            "UF_CRM_1656516949" => $row->outcome,
                            "UF_CRM_1656517133" => $row->profit,
                            "UF_CRM_1656516726" => $row->year,
                        )
                    );
                    $data = array_merge_recursive($data, $datafinance);
                }

                $data_string = json_encode($data, JSON_UNESCAPED_UNICODE);
                $curl = curl_init('https://portal.skillline.ru/rest/10/6zjmz9rvzkbr9b8t/crm.deal.add');
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json'
                    )
                );
                $result = curl_exec($curl);
                $objectB24ID = json_decode($result);
                $Bitrix24ID = $objectB24ID->result;


                if (isset($_GET['about_client'])) {
                    $domainID = $row->id;
                    $email = $row->email;
                    $phones = $row->number;
                    if (strlen($email) > 3) {
                        $splitEmails = explode(',', $email);
                        $splitPhones = explode(',', $phones);
                        $countEmails = count($splitEmails);
                        $countPhones = count($splitPhones);
                        $countElements = max(count($splitPhones), count($splitEmails));
                        if ($countEmails > $countPhones) {
                            $overlay = array_fill($countPhones, $countElements - $countPhones, ' ');
                            $EmailsAndPhones = array_combine($splitPhones + $overlay, $splitEmails);
                        } elseif ($countEmails < $countPhones) {
                            $overlay = array_fill($countEmails, $countElements - $countEmails, ' ');
                            $EmailsAndPhones = array_combine($splitPhones, $splitEmails + $overlay);
                        } else {
                            $EmailsAndPhones = array_combine($splitPhones, $splitEmails);
                        }
                        $dataContact = array();
                        foreach ($EmailsAndPhones as $phone => $email) {
                            $dataContact["fields"] = array(
                                "LAST_NAME" => $row->domain,
                                "EMAIL" => array(
                                    array(
                                        "VALUE" => $email
                                    )
                                ),
                                "PHONE" => array(
                                    array(
                                        "VALUE" => $phone
                                    )
                                ),
                            );
                            $dataContact_string = json_encode($dataContact, JSON_UNESCAPED_UNICODE);
                            $dataContact = [];
                            $curlAddContact = curl_init('https://portal.skillline.ru/rest/10/6zjmz9rvzkbr9b8t/crm.contact.add');
                            curl_setopt($curlAddContact, CURLOPT_CUSTOMREQUEST, "POST");
                            curl_setopt($curlAddContact, CURLOPT_POSTFIELDS, $dataContact_string);
                            curl_setopt($curlAddContact, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($curlAddContact, CURLOPT_HTTPHEADER, array(
                                    'Content-Type: application/json'
                                )
                            );
                            $resultAddContact = curl_exec($curlAddContact);
                            $objectContactID = json_decode($resultAddContact);
                            $ContactID = $objectContactID->result;
                            curl_close($curlAddContact);

                            $dataContactToDeal = array(
                                "ID" => $Bitrix24ID,
                                "fields" => array(
                                    "CONTACT_ID" => $ContactID
                                )
                            );
                            $dataContactToDeal_string = json_encode($dataContactToDeal, JSON_UNESCAPED_UNICODE);
                            $curlAddContactToDeal = curl_init('https://portal.skillline.ru/rest/10/6zjmz9rvzkbr9b8t/crm.deal.contact.add');
                            curl_setopt($curlAddContactToDeal, CURLOPT_CUSTOMREQUEST, "POST");
                            curl_setopt($curlAddContactToDeal, CURLOPT_POSTFIELDS, $dataContactToDeal_string);
                            curl_setopt($curlAddContactToDeal, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($curlAddContactToDeal, CURLOPT_HTTPHEADER, array(
                                    'Content-Type: application/json'
                                )
                            );
                            $resultAddContactToDeal = curl_exec($curlAddContactToDeal);
                            $objectContactID = json_decode($resultAddContactToDeal);
                            curl_close($curlAddContactToDeal);
                        }
                    }
                }
            }
            echo "<script>console.log('Data: " . $exportDomains . "' );</script>";
            return [
                'exportDomains' => $exportDomains,
            ];

        }
        //
        //Тестовая запись
        //
        else
            {
                if (isset($_GET['about_client']) || isset($_GET['about_site']) || isset($_GET['about_finance']) || isset($_GET['about_ur'])) {
                    $getConstructor = "domain domain_info.real_domain domain_info.id";
                    $exportDomains = cache()->remember('queryExport', 60 * 60 * 24, function () {
                        $getConstructor = "domain_info.real_domain title description keywords domain_info.city hosting cms ip year income outcome profit segment name type region domain_info.inn post_index boss_name boss_post main_activity domain_info.id";
                        return DB::table('domain_info')->
                        select(explode(' ', $getConstructor))->
                        addSelect("domain")->distinct()->
                        addselect(DB::raw("GROUP_CONCAT(DISTINCT email) as email"))->
                        addselect(DB::raw("GROUP_CONCAT(DISTINCT number) as number"))->
                        where('bitrix_id', '=', '2409257')->
                        leftJoin('domains', 'domains.id', '=', 'domain_info.domain_id')->
                        leftJoin('domain_emails', 'domain_emails.domain_id', '=', 'domain_info.domain_id')->
                        leftJoin('domain_phones', 'domain_phones.domain_id', '=', 'domain_info.domain_id')->
                        leftJoin('inns', 'inns.inn', '=', 'domain_info.inn')->
                        leftJoin('company_finances', 'company_finances.inn_id', '=', 'inns.id')->
                        leftJoin('company_info', 'company_info.inn_id', '=', 'inns.id')->
                        orderBy("year", 'DESC')->
                        groupBy(explode(' ', $getConstructor), "domain")->
                        limit(1)->get();
                    });
                    return [
                        'exportDomains' => $exportDomains,
                    ];
                }
            }

        // при init
        //cache()->forget('queryExport'); //для теста отключить кеширование запроса

        $exportDomains = cache()->remember('queryExport', 60*60*24, function(){
            $getConstructor = "domain_info.real_domain title description keywords domain_info.city hosting cms ip year income outcome profit segment name type region domain_info.inn post_index boss_name boss_post main_activity";
            return  DB::table('domain_info')->
            select(explode(' ', $getConstructor))->
            addSelect("domain")->distinct()->
            addselect(DB::raw("GROUP_CONCAT(DISTINCT email) as email"))->
            addselect(DB::raw("GROUP_CONCAT(DISTINCT number) as number"))->
            where('bitrix_id','=','2409257')->
            leftJoin('domains', 'domains.id' , '=', 'domain_info.domain_id')->
            leftJoin('domain_emails', 'domain_emails.domain_id', '=', 'domain_info.domain_id')->
            leftJoin('domain_phones', 'domain_phones.domain_id', '=', 'domain_info.domain_id')->
            leftJoin('inns', 'inns.inn', '=', 'domain_info.inn')->
            leftJoin('company_finances', 'company_finances.inn_id', '=', 'inns.id')->
            leftJoin('company_info', 'company_info.inn_id', '=', 'inns.id')->
            orderBy("year", 'DESC')->
            groupBy(explode(' ', $getConstructor), "domain")->
            limit(1)->get();
        });
        return [
            'exportDomains' => $exportDomains,
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Экспорт';
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::view('export'),
        ];
    }
}
