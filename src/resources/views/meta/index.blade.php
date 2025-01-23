@extends('waliby::layouts.app')

@section('content')
    <p>Use ~value~ form dynamic value | +~value~ if value is array | use #array# or #string# to determine request body format. Available dynamic params <b>token, phoneNumber, message, id, status</b></p>
    <hr>
    <form action="{{ route('waliby.metas.update') }}" class="form" method="post">
        <div class="row mb-2">
            <label for="header" class="form-label col-lg-2">Request Headers Format</label>
            <div class="col-lg-10">
                <textarea class="form-control" name="header" id="header"></textarea>
            </div>
        </div>
        <div class="row mb-2">
            <label for="header" class="form-label col-lg-2">Request Body Format</label>
            <div class="col-lg-10">
                <textarea class="form-control" name="header" id="header"></textarea>
            </div>
        </div>
        <div class="row mb-2">
            <label for="header" class="form-label col-lg-2">Response Format</label>
            <div class="col-lg-10">
                <textarea class="form-control" name="header" id="header"></textarea>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>

    </script>
@endpush