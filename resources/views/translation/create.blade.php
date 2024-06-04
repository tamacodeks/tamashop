@extends('layout.app')
@section('content')
    @include('layout.breadcrumb',['data' => [
        ['name' => "Translation",'url'=> secure_url('translation'),'active' => 'no'],
        ['name' => "Create",'url'=> '','active' => 'yes']
    ]
    ])
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Add New Translation</div>

                    <div class="panel-body">
                        <form class="form-horizontal" action="{{ secure_url('translation/add') }}" method="POST">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label for="name" class=" control-label col-md-4"> Language Name *</label>
                                <div class="col-md-5">
                                    <input name="name" type="text" id="name" class="form-control input-sm"
                                           required="" value="{{ old('name') }}"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="folder" class=" control-label col-md-4"> Folder Name *</label>
                                <div class="col-md-5">
                                    <input value="{{ old('folder') }}" name="folder" type="text" id="folder"
                                           class="form-control input-sm" required/>
                                    <span class="help-block">
                                            <span class="text-info">(Please prefer country iso code as folder name!)</span>
                                        </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="author" class=" control-label col-md-4"> Author</label>
                                <div class="col-md-5">
                                    <input name="author" value="{{ old('author') }}" type="text" id="author"
                                           class="form-control input-sm" required/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="ipt" class=" control-label col-md-4"> </label>
                                <div class="col-md-8">
                                    <button type="submit" name="submit" class="btn btn-info">Save Changes</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection