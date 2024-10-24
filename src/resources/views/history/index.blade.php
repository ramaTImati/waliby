@extends('waliby::layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
    <table class="table table-bordered" id="table">
        <thead>
            <td class="text-center">#</td>
            <td class="text-center">Message ID</td>
            <td class="text-center">Phone Number</td>
            <td class="text-center">Message</td>
            <td class="text-center">Status</td>
        </thead>
    </table>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function(){
            $("#table").DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('waliby.history.index') }}',
                columns: [
                    {data: 'DT_RowIndex'},
                    {data: 'message_id'},
                    {data: 'phone_number'},
                    {data: 'message_text'},
                    {data: 'status'}
                ]
            })
        })
    </script>
@endpush