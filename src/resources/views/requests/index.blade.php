@extends('waliby::layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.2/dist/sweetalert2.min.css">
@endpush

@section('content')
    <button type="button" class="btn btn-success mb-2" id="createFormButton">Create</button>
    <div id="createField" class="border rounded p-2" style="display:none">
        <form action="#" id="payloadForm">
            @csrf
            <div class="mb-3">
                <label for="basetable" class="form-label">Request Name</label>
                <input type="text" name="requestName" id="requestName" class="form-control">
                <small class="text-secondary">write with camel case and don't use space</small>
            </div>
            <div class="mb-3">
                <label for="exampleFormControlTextarea1" class="form-label">Request Payload Structure</label>
                <textarea class="form-control" name="payloadStructure" id="payloadStructure" rows="5"></textarea>
                <small class="text-secondary">fill with php array format!</small>
            </div>
            <div class="row">
                <div class="col">
                    <button type="submit" class="btn btn-success float-end mx-1">Save</button>
                    <button type="button" class="btn btn-warning float-end mx-1" id="cancelFormButton">Cancel</button>
                </div>
            </div>
        </form>
    </div>
    <hr>
    <table class="table table-bordered" id="table">
        <thead>
            <td class="text-center">#</td>
            <td class="text-center">Name</td>
            <td class="text-center">Payload</td>
        </thead>
    </table>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.2/dist/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function(){

        })

        let table = $("#table").DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('waliby.templates.index') }}',
            columns: [
                {data: 'DT_RowIndex'},
                {data: 'uuid'},
                {data: 'message'},
                {data: 'created_by'},
                {data: 'action', orderable: false, searchable: false}
            ]
        })

        $("#createFormButton").click(function(){
            $("#createField").slideDown()
        })

        $("#cancelFormButton").click(function(){
            $("#createField").slideUp()
        })

        $("#payloadForm").submit(function(event){
            event.preventDefault()
            let fd = new FormData(this)
            $.ajax({
                url: "{{ route('waliby.requests.store') }}",
                method: "POST",
                data: fd,
                dataType: "JSON",
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function(pre){
                    Swal.fire({
                        title: 'Loading',
                        allowEscapeKey: false,
                        allowOutsideClick: false
                    })
                    Swal.showLoading();
                },
                success: function(response){
                    console.log(response);
                    $("#createField").slideUp()
                    $("#payloadForm")[0].reset()
                    table.draw()
                    Swal.fire({
                        title: "Success",
                        icon: "success",
                        text: response.message
                    })
                },
                error: function(e){
                    console.log(e);
                    Swal.fire({
                        title: "Error",
                        icon: "error",
                        text: e.message
                    })
                }
            })
        })
    </script>
@endpush