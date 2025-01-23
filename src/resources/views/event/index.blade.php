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
                <label for="selectReceiver" class="form-label">Receiver Parameters</label>
                <select name="receiverParams" id="selectReceiver" class="form-control"></select>
            </div>
            <div class="mb-3">
                <label for="eventType" class="form-label">Event Type</label>
                <select name="eventType" id="eventType" class="form-select" required>
                    <option value="manual" selected>Manually</option>
                    <option value="recurring">Recurring</option>
                </select>
            </div>
            <div class="row mb-3" id="recurringType">
                <div class="col-md-6">
                    <label for="scheduledEvery" class="form-label">Scheduled Every</label>
                    <select name="scheduledEvery" id="scheduledEvery" class="form-select">
                        <option value=""hidden readonly disable>--- Select ---</option>
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="scheduledAt" class="form-label">Scheduled At <small class="text-muted" id="scheduledAtDesc"></small></label>
                    <input type="number" name="scheduledAt" id="scheduledAt" class="form-control">
                </div>
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
            <td class="text-center">Last Processed</td>
            <td class="text-center">Action</td>
        </thead>
    </table>

    <!-- START DETAIL MODAL -->
        <div class="modal fade" id="detailModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailEventName"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="detailEventMessage" class="form-label">Message</label>
                            <textarea id="detailEventMessage" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="detailReceiverParams" class="form-label">Parameters</label>
                            <textarea id="detailReceiverParams" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="detailReceiverPanel" class="form-label">Receiver</label>
                            <div class="card">
                                <div class="card-body" id="detailReceiverPanel">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    <!-- END DETAIL MODAL -->
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.2/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function(){
            let eventType = $("#eventType").val()
            if (eventType == 'manual') {
                $("#recurringType").slideUp()
            }
        })

        let table = $("#table").DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('waliby.events.index') }}',
            columns: [
                {data: 'DT_RowIndex'},
                {data: 'event_name'},
                {data: 'template.message'},
                {data: 'last_processed'},
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
                url: "{{ route('waliby.events.store') }}",
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
                    $("#recurringType").slideUp()
                    $("#selectReceiver").val('').trigger('change')
                    $("#messageTemplate").val('').trigger('change')
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
                        text: e.responseJSON.message
                    })
                }
            })
        })

        $("#selectReceiver").select2({
            theme: "bootstrap-5",
            placeholder: "--- Select Receiver Parameters ---",
            minimumResultsForSearch: Infinity,
            maximumInputLength: 0,
            allowClear: true,
            width: "100%",
            closeOnSelect: true,
            ajax: {
                url: "{{ route('waliby.events.getReceiver') }}",
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
                url: "{{ route('waliby.events.getMessageTemplate') }}",
                processResults: function(data){
                    return {
                        results: data
                    }
                }
            }
        })

        function detailEvent(id){
            $("#detailModal").modal('show')
            $.ajax({
                url: "{{ url('waliby/events/show') }}/"+id,
                method: "GET",
                success: function(res){                    
                    $("#detailEventName").text(res.event_name)
                    $("#detailEventMessage").text(res.message)
                    $("#detailReceiverParams").text(res.parameters)
                    let receiver = res.receiver
                    
                    receiver.map(function(v){
                        $("#detailReceiverPanel").append(`<span class="badge text-bg-secondary">`+v.name+`</span> `)
                    })
                },
                error: function(e){
                    console.log(e);
                }
            })
        }

        $("#scheduledEvery").change(function(){
            value = $(this).val()
            if (value == 'daily') {
                $("#scheduledAtDesc").text('24 hours format')
                $("#scheduledAt").attr({"min":1,"max":24})
            }else if(value == 'weekly'){
                $("#scheduledAtDesc").text('7 days of week')
                $("#scheduledAt").attr({"min":1,"max":7})
            }else if(value == 'monthly'){
                $("#scheduledAtDesc").text('')
                $("#scheduledAt").attr({"min":1,"max":28})
            }else if(value == 'yearly'){
                $("#scheduledAtDesc").text('12 month of year')
                $("#scheduledAt").attr({"min":1,"max":12})
            }
        })

        $("#eventType").change(function(){
            value = $(this).val()
            if (value == 'recurring') {
                $("#recurringType").slideDown()
            }else{
                $("#recurringType").slideUp()
            }
        })
    </script>
@endpush