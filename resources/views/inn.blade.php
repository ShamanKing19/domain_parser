<head>
    <title>ИНН</title>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
<div class="content">
    <form type="get" class="form-inn" id="form-inn">
        <div class="input-group mb-3">
            <input class=" form-control" name="inn" type="search">
            <div class="input-group-append">
                <input class="btn btn-info" type="submit" value="Найти">
            </div>
        </div>
    </form>
    <table class="table table-striped">
        <tbody class="table-parser-body">
        <?if(isset($_GET['inn'])){
        ?>
        @foreach($searchINN as $row)
            @once
                <tr>
                    <td>
                        ИНН
                    </td>
                    <td>
                        {{$_GET['inn']}}
                    </td>
                </tr>
                <tr>
                    <td>
                        Название
                    </td>
                    <td>
                        {{$row->name}}
                    </td>
                </tr>
                <tr>
                    <td>
                        Тип
                    </td>
                    <td>
                        {{$row->type}}
                    </td>
                </tr>
                <tr>
                    <td>
                        Регион
                    </td>
                    <td>
                        {{$row->region}}
                    </td>
                </tr>
                <tr>
                    <td>
                        Адрес
                    </td>
                    <td>
                        {{$row->address}}
                    </td>
                </tr>
                <tr>
                    <td>
                        Направление
                    </td>
                    <td>
                        {{$row->main_activity}}
                    </td>
                </tr>
                <tr>
                    <td>
                        Год
                    </td>
                    <td>
                        {{$row->year}}
                    </td>
                </tr>
                <tr>
                    <td>
                        Доходы
                    </td>
                    <td>
                        {{$row->income . ' млн. рублей'}}
                    </td>
                </tr>
                <tr>
                    <td>
                        Расходы
                    </td>
                    <td>
                        {{$row->outcome . ' млн. рублей'}}
                    </td>
                </tr>
                <tr>
                    <td>
                        Прибыль
                    </td>
                    <td>
                        {{$row->profit . ' млн. рублей'}}
                    </td>
                </tr>
                <tr>
                    <td>
                        Директор
                    </td>
                    <td>
                        {{$row->boss_name}}
                    </td>
                </tr>
            @endonce
        @endforeach
        <?
        }?>
        </tbody>
    </table>
</div>
