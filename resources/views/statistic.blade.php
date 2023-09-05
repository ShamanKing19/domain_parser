<head>
    <title>Статистика</title>
</head>
<body>
<div class="row">
    <div class="table-responsive col-md-6">
        <table class="table table-striped">
            <thead style="border-bottom: 2px solid #999999">
            <th scope="col">
                CMS
            </th>
            <th scope="col">
                Количество
            </th>
            </thead>
            <tbody>
            @foreach($searchStatisticCMS as $row)
                <tr>
                    <td>
                        {{$row->cms}}
                    </td>
                    <td>
                        {{$row->total}}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="table-responsive col-md-6">
        <table class="table table-striped">
            <thead style="border-bottom: 2px solid #999999">
            <th scope="col">
                Статус
            </th>
            <th scope="col">
                Количество
            </th>
            </thead>
            <tbody>
            @foreach($searchStatisticStatus as $row)
                <tr>
                    <td>
                        {{$row->status}}
                    </td>
                    <td>
                        {{$row->total}}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
</body>
@section('scripts')
    <script>

    </script>
@endsection
