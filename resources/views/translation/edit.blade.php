@extends('layout.app')
@section('content')
    @include('layout.breadcrumb',['data' => [
        ['name' => "Translation",'url'=> secure_url('translation'),'active' => 'no'],
        ['name' => "Edit ".$file,'url'=> '','active' => 'yes']
    ]
    ])
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Add New Translation</div>

                    <div class="panel-body">
                        <ul class="nav nav-tabs">
                            @foreach($files as $f)
                                @if($f != "." and $f != ".." and $f != 'config.json' and $f != 'validation.php')
                                    <li @if($file == $f) class="active" @endif >
                                        <a href="{{ secure_url('translation?edit='.$lang.'&file='.$f)}}">{{ $f }} </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                        <form class="form-horizontal" action="{{ secure_url('translation/save') }}" method="POST">
                            {{ csrf_field() }}
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th> Phrase</th>
                                    <th> Translation</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($stringLang as $key => $val) :
                                if(!is_array($val))
                                {
                                ?>
                                <tr>
                                    <td><?php echo $key;?></td>
                                    <td><input type="text" name="<?php echo $key;?>" value="<?php echo $val;?>"
                                               class="form-control"/>
                                    </td>
                                </tr>
                                <?php
                                } else {
                                foreach($val as $k=>$v)
                                { ?>
                                <tr>
                                    <td><?php echo $key . ' - ' . $k;?></td>
                                    <td><input type="text" name="<?php echo $key;?>[<?php echo $k;?>]"
                                               value="<?php echo $v;?>" class="form-control"/>
                                    </td>
                                </tr>
                                <?php }
                                }
                                endforeach; ?>
                                </tbody>

                            </table>
                            <input type="hidden" name="lang" value="{{ $lang }}"/>
                            <input type="hidden" name="file" value="{{ $file }}"/>
                            <button type="submit" class="btn btn-info"> Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection