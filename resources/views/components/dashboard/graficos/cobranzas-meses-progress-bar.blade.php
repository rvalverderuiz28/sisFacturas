<ul class="list-group">
    <li class="list-group-item" style=" min-width: 300px; ">
        <h5>COBRANZAS <br>{{Str::upper($title)}}  a  {{Str::upper($startDate->clone()->addMonths(4)->monthName)}} - {{$startDate->clone()->addMonths(4)->year}}</h5>
    </li>
    @dump($general)
    {{--
    @if(data_get($general,'enabled'))
        <li class="list-group-item" style=" background: #b7b7b7; ">
            <b> {{data_get($general,'name')}}</b>
            <x-bs-progressbar :progress="data_get($general,'progress')">
                {{ data_get($general,'progress')}}% - {{data_get($general,'pagados')}} /{{data_get($general,'activos')}}
            </x-bs-progressbar>
        </li>
    @endif
    --}}
    <li class="list-group-item" style=" background: #b7b7b7; ">
        <b>{{collect($general)->values()->get(0)['name']}}</b>

        <div class="row">
            @foreach($general as $datestr=>$data)
                <div class="col-md-3">
                    <x-bs-progressbar :progress="data_get($data,'progress')">
                        <p><b>{{$datestr}} | {{$data['progress']}}% - {{$data['pagados']}}/{{$data['activos']}}</b></p>
                    </x-bs-progressbar>
                </div>
            @endforeach
        </div>
    </li>
    @foreach($progressData as $identificador=>$dataall)
        <li class="list-group-item" @if($loop->index%2==0) style="background: #ffffff4f" @endif>
            <b>{{$identificador}}</b> <br> {{collect($dataall)->values()->get(0)['name']}}

            <div class="row">
                @foreach($dataall as $datestr=>$data)
                    <div class="col-md-3">
                        <x-bs-progressbar :progress="data_get($data,'progress')">
                            <p><b>{{data_get($data,'date')->format('m-Y')}} | {{$data['progress']}}% - {{$data['pagados']}}/{{$data['activos']}}</b></p>
                        </x-bs-progressbar>
                    </div>
                @endforeach
            </div>
            <span>% - Cobrados / Asignados</span>
        </li>
    @endforeach
</ul>