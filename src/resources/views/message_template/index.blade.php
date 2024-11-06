@extends('waliby::layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.2/dist/sweetalert2.min.css">
@endpush

@section('content')
    <button type="button" class="btn btn-success mb-2" id="createFormButton">Create</button>
    <div id="createField" class="border rounded p-2" style="display:none">
        <form action="#" id="messageTemplateForm">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Template Name</label>
                <input type="text" name="name" id="name" class="form-control">
            </div>
            <div class="mb-3">
                <label for="exampleFormControlTextarea1" class="form-label">Message Template</label>
                <textarea class="form-control" name="template" id="exampleFormControlTextarea1" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <ul>
                    <li class="text-secondary"><small>Write ~params~ to use dynamic messages</small></li>
                    <li class="text-secondary">
                        <small>
                            available dynamic parameters : 
                            @forelse($column as $row) 
                                <b>{{$row}}</b>
                                @if(!$loop->last)
                                ,
                                @endif
                            @empty 
                                no parameters!
                            @endforelse
                        </small>
                    </li>
                </ul>
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
            <td class="text-center">Id</td>
            <td class="text-center">Name</td>
            <td class="text-center">Text</td>
            <td class="text-center">Created By</td>
            <td class="text-center">Action</td>
        </thead>
    </table>
    <!-- START EDIT MODAL -->
        <div class="modal fade" id="editModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Template</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="#" id="updateTemplateForm">
                        @csrf @method('PUT')
                        <input type="hidden" name="templateId" id="editTemplateId">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="editTemplate" class="form-label">Text</label>
                                <textarea id="editTemplate" name="text" class="form-control"></textarea>
                            </div>
                            <div class="mb-3">
                                <ul>
                                    <li class="text-dark"><small>Write ~params~ to use dynamic messages</small></li>
                                    <li class="text-dark">
                                        <small>
                                            available dynamic parameters : 
                                            @forelse($column as $row) 
                                                <b>{{$row}}</b>
                                                @if(!$loop->last)
                                                ,
                                                @endif
                                            @empty 
                                                no parameters!
                                            @endforelse
                                        </small>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="Submit" class="btn btn-primary">Update</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <!-- END EDIT MODAL -->
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

        $("#messageTemplateForm").submit(function(event){
            event.preventDefault()
            let fd = new FormData(this)
            $.ajax({
                url: "{{ route('waliby.templates.store') }}",
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
                    $("#messageTemplateForm")[0].reset()
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

        function edit(id){
            $("#editModal").modal('show')
            $.ajax({
                url: "{{ url('waliby/templates/show') }}/"+id,
                method: "GET",
                success: function(res){
                    $("#editTemplate").text(res.message)
                    $("#editTemplateId").val(id)
                },
                error: function(e){
                    console.log(e);
                }
            })
        }

        $("#updateTemplateForm").submit(function(event){
            event.preventDefault()
            let fd = new FormData(this)
            id = $("#editTemplateId").val()
            $.ajax({
                url: "{{ url('waliby/templates/update/') }}/"+id,
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
                    $("#editModal").modal('hide')
                    $("#updateTemplateForm")[0].reset()
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