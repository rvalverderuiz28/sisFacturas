<div class="card">
    <div class="card-body">
        <div id="{{$genId}}"></div>
    </div>
</div>
@push('js')
    <script>
        var chart = new ApexCharts(document.querySelector("#{{$genId}}"), {{\Illuminate\Support\Js::from($configuration)}});
        chart.render();
    </script>
@endpush
