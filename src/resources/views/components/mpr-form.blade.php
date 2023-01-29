<form action="{{$route}}" class="mprForm">

    <div class="col-12 text-danger errorDiv " style="display: none;">ERRR</div>
    <div class="col-12 text-success goodDiv " style="display: none;">Данные сохранены</div>

    {{$slot}}


    <div class="col-12  " style="text-align: right;">
        <div class="spinner-border float-end loadingDiv" role="status" style="display: none;">
            <span class="visually-hidden">Loading...</span>
        </div>
        <a class="btn btnSumbitForm btn-outline-dark mt-4  w100">{{$btn}}</a>
    </div>
</form>
