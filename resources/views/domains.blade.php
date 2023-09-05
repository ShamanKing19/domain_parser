<head>
    <title>Домены</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="{{asset("css/parser.css") }}">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>
<body>

<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form class="form-parser" type="get">
                    <div class="input-group mb-3">
                        <input name="searchDomain" placeholder="Домен" id="searchDomain" type="search" class="form-control" aria-describedby="basic-addon1">
                    </div>
                    <div class="input-group mb-3">
                        <input name="searchTitle" placeholder="Заголовок" id="searchTitle" type="search" class="form-control" aria-describedby="basic-addon1">
                    </div>
                    <div class="input-group mb-3">
                        <input name="searchCMS" placeholder="CMS" id="searchCMS" type="search" class="form-control" aria-describedby="basic-addon1">
                    </div>
                    <div class="input-group mb-3">
                        <input name="searchRegion" placeholder="Регион" id="searchRegion" type="search" class="form-control" aria-describedby="basic-addon1">
                    </div>
                    <button type="submit" class="btn btn-info"  style="margin-left: 40%">Найти</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="parser-card-header">
    <h4>Домены</h4>
    <input class="searchInput" data-toggle="modal" data-target="#exampleModalCenter">
</div>
<?if($_GET){?>
<table class="table table-striped">
    <thead>
    <tr>
        <th>Домен</th>
        <th>Заголовок</th>
        <th>Описание</th>
        <th>Регион</th>
        <th>CMS</th>
    </tr>
    </thead>
    <tbody>
    @foreach($searchParser as $row)
        <tr>
            <td>
                <a href="//{{($row->real_domain)}}">{{$row->real_domain}}</a>
            </td>
            <td style="width: 17%;">
                {{$row->title}}
            </td>
            <td style="width: 20%;">
                {{$row->description}}
            </td>
            <td>
                {{$row->city}}
            </td>
            <td style="width: 9%;">
                {{$row->cms}}
            </td>
        </tr>
    @endforeach
    {{ $searchParser->links() }}
    </tbody>
</table>
<?}?>
</body>
<script>

</script>

