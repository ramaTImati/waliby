@extends('waliby::layouts.app')

@section('content')
    <p>Use ~value~ for dynamic value | +~value~ if value is array | use #array# or #string# to determine request body format. Available dynamic params <b>token, phoneNumber, message, id, status</b></p>
    <hr>
    <form action="{{ route('waliby.metas.update') }}" class="form p-2" method="post">
        @csrf
        <div class="row mb-2">
            <label for="header" class="form-label col-lg-2">Request Headers Format</label>
            <div class="col-lg-10">
                <textarea class="form-control" name="header" id="header" placeholder="Type Here..">{{$res['REQUEST_HEADERS']}}</textarea>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-2">Example</div>
            <div class="col-lg-10">
                <div class="row px-3">
                    <div class="col-lg-5 border">
                        <pre>
                            <code>
{
    "Authorization": "y898h4tbn#7h%",
    "Content-Type": "application/json"
}
                            </code>
                        </pre>
                    </div>
                    <div class="col-lg-2">
                        <span class="">write request header format like this =></span>
                    </div>
                    <div class="col-lg-5 border">
                        <pre>
                            <code>
Authorization=~token~,Content-Type=application/json
                            </code>
                        </pre>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        
        <!-- END REQUEST HEADER -->
        <!-- START REQUEST BODY -->
        <div class="row mb-2">
            <label for="header" class="form-label col-lg-2">Request Body Format</label>
            <div class="col-lg-10">
                <textarea class="form-control" name="body" id="body" placeholder="Type Here..">{{$res['REQUEST_BODY']}}</textarea>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-2">Example</div>
            <div class="col-lg-10">
                <div class="row px-3">
                    <div class="col-lg-5 border">
                        <pre>
                            <code>
{
    "data": "[{"target": "082227097005", "message": "1"},{"target": "082227097005", "message": "2"}]"
}
                            </code>
                        </pre>
                    </div>
                    <div class="col-lg-2">
                        <span class="">write request body format like this =></span>
                    </div>
                    <div class="col-lg-5 border">
                        <pre>
                            <code>
data=+#string#target=~phoneNumber~,message=~message~
                            </code>
                        </pre>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-2"></div>
            <div class="col-lg-10">
                <div class="row px-3">
                    <div class="col-lg-5 border">
                        <pre>
                            <code>
{
    "data": [
        {"target": "082227097005", "message": "1"},
        {"target": "082227097005", "message": "2"}
    ]
}
                            </code>
                        </pre>
                    </div>
                    <div class="col-lg-2">
                        <span class="">write request body format like this =></span>
                    </div>
                    <div class="col-lg-5 border">
                        <pre>
                            <code>
data=+#array#target=~phoneNumber~,message=~message~
                            </code>
                        </pre>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <!-- END REQUEST BODY -->
        <!-- START RESPONSE FORMAT -->
        <div class="row mb-2">
            <label for="header" class="form-label col-lg-2">Response Format</label>
            <div class="col-lg-10">
                <textarea class="form-control" name="response" id="response" placeholder="Type Here..">{{$res['RESPONSE']}}</textarea>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-2">Example</div>
            <div class="col-lg-10">
                <div class="row px-3">
                    <div class="col-lg-5 border">
                        <pre>
                            <code>
{
  "detail": "success! message in queue",
  "id": [
    "78085918",
    "78085919"
  ],
  "process": "pending",
  "status": true,
  "target": [
    "082227097005",
    "082227097005"
  ]
}
                            </code>
                        </pre>
                    </div>
                    <div class="col-lg-2">
                        <span class="">write response format like this =></span>
                    </div>
                    <div class="col-lg-5 border">
                        <pre>
                            <code>
{"detail":"success! message in queue","id":"~id~","process":"~status~","status":"true","target":"~phoneNumber~"}
                            </code>
                        </pre>
                    </div>
                </div>
            </div>
        </div>
        <!-- END RESPONSE FORMAT -->
        <button type="submit" class="btn btn-primary w-100 my-3">Save</button>
    </form>
@endsection

@push('scripts')
    <script>

    </script>
@endpush