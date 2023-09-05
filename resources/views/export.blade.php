<head>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
    <div class="content">
        <table class="table table-striped">
            <thead>
            <tr>
                <td>
                    <b>Информация о клиенте:</b> почта и телефон в отдельной карточке
                </td>
            </tr>
            <tr>
                <td>
                    <b>О сайте:</b> реальный домен, название, описание, ключевые слова, почта, телефон, регион, хостинг, cms, ip
                </td>
            </tr>
            <tr>
                <td>
                    <b>Финансовые показатели:</b> финансовый год, доходы, расходы, прибыль, уставной капитал, сегмент
                </td>
            </tr>
            <tr>
                <td>
                    <b>Информация о юр. лице:</b> тип компании, тип предприятия, вид деятельности, директор, должность, юр. адрес, инн, почтовый индекс...
                </td>
            </tr>
            </thead>
        </table>
        <table class="crm-info" style="max-height: 90%;overflow-y: scroll">
            <table class="table table-striped">
                <form method="get" id="confirmExport">
                    <tbody class="table-parser-body">
                    <tr>
                        <td>
                            <select class="form-select crm-select">
                                <option>Добавить</option>
                            </select>
                        </td>
                        <td>
                            <label class="form-check form-switch" onchange="this.form.submit();">
                                <input class="form-check-input" type="checkbox" id="about_client" name="about_client" <?php if(isset($_GET['about_client'])) echo'checked'?>>
                                <span class="form-check-label" style="border:none">Информация о клиенте</span>
                            </label>
                        </td>
                        <td>
                            <label class="form-check form-switch" onchange="this.form.submit();">
                                <input class="form-check-input" type="checkbox" id="about_site" name="about_site" <?php if(isset($_GET['about_site'])) echo'checked'?>>
                                <span style="border:none">О сайте</span>
                            </label>
                        </td>
                        <td>
                            <label class="form-check form-switch" onchange="this.form.submit();">
                                <input class="form-check-input" type="checkbox" id="about_finance" name="about_finance" <?php if(isset($_GET['about_finance'])) echo'checked'?>>
                                <span style="border:none; color: red">Финансовые показатели</span>
                            </label>
                        </td>
                        <td>
                            <label class="form-check form-switch" onchange="this.form.submit();">
                                <input class="form-check-input" type="checkbox" id="about_ur" name="about_ur" <?php if(isset($_GET['about_ur'])) echo'checked'?>>
                                <span style="border:none; color:red">Информация о юр. лице</span>
                            </label>
                        </td>
                        <td>
                            <select class="form-select crm-select">
                                <option>10</option>
                                <option>50</option>
                                <option>100</option>
                                <option>500</option>
                            </select>
                        </td>
                    </tr>
                    </tbody>
                    <input class="btn btn-info" style="margin-bottom: 10px" type="submit" name="confirmExport" value="Выгрузить">
                </form>
                <table class="table table-striped">
                    <thead class="table-parser-head">
                    <td>
                        Пример выгружаемых данных
                    </td>
                    </thead>
                    <tbody class="table-parser-body">
                    <tr>
                        @foreach($exportDomains as $row)
                            <td>
                                Домен: {{$row->domain}}
                            </td>
                            <td>
                                Реальный домен: {{$row->real_domain}}
                            </td>
                        @endforeach
                    </tr>
                    </tbody>
                </table>
                <?php
                if(isset($_GET['about_client'])){
                ?>
                <table class="table table-striped">
                    <thead class="table-parser-head">
                    <td>
                        Пример информации о клиенте
                    </td>
                    </thead>
                    <tbody class="table-parser-body">
                    @foreach($exportDomains as $row)
                        <tr>
                            <td>
                                Почта (для карточки клиент): {{$row->email}}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Телефон (для карточки клиент): {{$row->number}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <?php
                }
                ?>
                <?php
                if(isset($_GET['about_site'])){
                ?>
                <table class="table table-striped">
                    <thead class="table-parser-head">
                    <td>
                        Пример информации о сайте
                    </td>
                    </thead>
                    <tbody class="table-parser-body">
                    @foreach($exportDomains as $row)
                        <tr>
                            <td>
                                Название сайта: {{$row->title}}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Описание: {{$row->description}}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Ключевые слова: {{$row->keywords}}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Регион: {{$row->city}}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Хостинг: {{$row->hosting}}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                CMS: {{$row->cms}}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                IP: {{$row->ip}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <?php
                }
                ?>
                <?php
                if(isset($_GET['about_finance'])){
                ?>
                <table class="table table-striped">
                    <thead class="table-parser-head">
                    <td>
                        Пример финансовых показателей
                    </td>
                    </thead>
                    <tbody class="table-parser-body">
                    @foreach($exportDomains as $row)
                        <tr>
                            <td>
                                Последний финансовый год: {{$row->year}}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Доходы: {{$row->income}} млн. рублей
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Расходы: {{$row->outcome}} млн. рублей
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Прибыль: {{$row->profit}} млн. рублей
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Сегмент: {{$row->segment}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <?php
                }
                ?>
                <?php
                if(isset($_GET['about_ur'])){
                ?>
                <table class="table table-striped">
                    <thead class="table-parser-head">
                    <td>
                        Пример юридической информации
                    </td>
                    </thead>
                    <tbody class="table-parser-body">
                    @foreach($exportDomains as $row)
                        <tr>
                            <td>
                                Полное наименование: {{$row->name}}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Тип компании: {{$row->type}}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Направление деятельности: {{$row->main_activity}}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Регион регистрации: {{$row->region}}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                ИНН: {{$row->inn}}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Почтовый индекс: {{$row->post_index}}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Директор: {{$row->boss_name}}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Должность: {{$row->boss_post}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <?php
                }
                ?>
            </table>
        </table>
    </div>
@section('scripts')

@endsection
