@extends('waliby::layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.2/dist/sweetalert2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush

@section('content')
    <button type="button" class="btn btn-success mb-2" id="createFormButton">Create</button>
    <div id="createField" class="border rounded p-2" style="display:none">
        <form action="#" id="messageEventForm">
            @csrf
            <div class="mb-3">
                <label for="eventname" class="form-label">Event Name</label>
                <input type="text" name="eventname" id="eventname" class="form-control">
            </div>
            <div class="mb-3">
                <label for="messageTemplate" class="form-label">Message Template</label>
                <select name="messageTemplate" id="messageTemplate" class="form-control"></select>
            </div>
            <div class="mb-3">
                <label for="receiver" class="form-label">Receiver</label>
                <select name="receiver[]" id="selectReceiver" class="form-control"></select>
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
            <td class="text-center">Event Name</td>
            <td class="text-center">Message Template Id</td>
            <td class="text-center">Event Status</td>
            <td class="text-center">Action</td>
        </thead>
    </table>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.2/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function(){

        })

        let table = $("#table").DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('waliby.event.index') }}',
            columns: [
                {data: 'DT_RowIndex'},
                {data: 'event_name'},
                {data: 'message_template_id'},
                {data: 'event_status'},
                {data: 'action', orderable: false, searchable: false}
            ]
        })

        $("#createFormButton").click(function(){
            $("#createField").slideDown()
        })

        $("#cancelFormButton").click(function(){
            $("#createField").slideUp()
        })

        $("#messageEventForm").submit(function(event){
            event.preventDefault()
            let fd = new FormData(this)
            $.ajax({
                url: "{{ route('waliby.event.store') }}",
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
                    $("#messageEventForm")[0].reset()
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

        $("#selectReceiver").select2({
            theme: "bootstrap-5",
            placeholder: "--- Select Receiver ---",
            allowClear: true,
            multiple: true,
            width: "100%",
            closeOnSelect: false,
            ajax: {
                url: "{{ route('waliby.event.getReceiver') }}",
                processResults: function(data){
                    return {
                        results: data
                    }
                }
            }
        })

        $("#messageTemplate").select2({
            theme: "bootstrap-5",
            placeholder: "--- Select Message Template ---",
            allowClear: true,
            width: "100%",
            ajax: {
                url: "{{ route('waliby.event.getMessageTemplate') }}",
                processResults: function(data){
                    return {
                        results: data
                    }
                }
            }
        })

        function sent(id){
            
        }
    </script>
@endpush