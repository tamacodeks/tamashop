@extends('layout.app')
@section('content')
    <style>

        /*Nestable lists*/
        .dd {
            position: relative;
            display: block;
            margin: 0;
            padding: 0;
            max-width: 600px;
            list-style: none;
            font-size: 13px;
            line-height: 20px;
        }

        .dd-list {
            display: block;
            position: relative;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .dd-list .dd-list {
            padding-left: 30px;
        }

        .dd-collapsed .dd-list {
            display: none;
        }

        .dd-item,
        .dd-empty,
        .dd-placeholder {
            display: block;
            position: relative;
            margin: 0;
            padding: 0;
            min-height: 30px;
            font-size: 13px;
            line-height: 25px;
        }

        .dd-handle {
            cursor: default;
            display: block;
            margin: 5px 0;
            padding: 7px 10px;
            color: #333;
            text-decoration: none;
            border: 1px solid #ddd;
            background: #fff;
        }

        .dd-handle:hover {
            color: #FFF;
            background: #4D90FD;
            border-color: #428BCA;
        }

        .dd-item > button {
            color: #555;
            font-family: FontAwesome;
            display: block;
            position: relative;
            cursor: pointer;
            float: left;
            width: 25px;
            height: 20px;
            margin: 8px 2px;
            padding: 0;
            text-indent: 100%;
            white-space: nowrap;
            overflow: hidden;
            border: 0;
            background: transparent;
            font-size: 10px;
            line-height: 1;
            text-align: center;
        }

        .dd-item > button:before {
            display: block;
            position: absolute;
            width: 100%;
            text-align: center;
            text-indent: 0;
        }

        .dd-item > button[data-action="collapse"]:before {
        }

        .dd-placeholder,
        .dd-empty {
            margin: 5px 0;
            padding: 0;
            min-height: 30px;
            background: #FFF;
            border: 1px dashed #b6bcbf;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }

        .dd-empty {
            border: 1px dashed #bbb;
            min-height: 100px;
            background-color: #e5e5e5;
            background-image: -webkit-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff),
            -webkit-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff);
            background-image: -moz-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff),
            -moz-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff);
            background-image: linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff),
            linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff);
            background-size: 60px 60px;
            background-position: 0 0, 30px 30px;
        }

        .dd-dragel {
            position: absolute;
            pointer-events: none;
            z-index: 9999;
        }

        .dd-dragel > .dd-item .dd-handle {
            margin-top: 0;
        }

        .dd-dragel .dd-handle {
            -webkit-box-shadow: 2px 4px 6px 0 rgba(0, 0, 0, .1);
            box-shadow: 2px 4px 6px 0 rgba(0, 0, 0, .1);
        }

        .dd3-content {
            display: block;
            margin: 5px 0;
            padding: 2px 10px 2px 40px;
            color: #333;
            text-decoration: none;
            background: none repeat scroll 0 0 #FFFFFF;
            border: 1px solid #DDDDDD;
            color: #333333;

            /* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#fcfff4+0,e9e9ce+100;Wax+3D+%232 */
            background: #fcfff4; /* Old browsers */
            background: -moz-linear-gradient(top, #fcfff4 0%, #e9e9ce 100%); /* FF3.6-15 */
            background: -webkit-linear-gradient(top, #fcfff4 0%, #e9e9ce 100%); /* Chrome10-25,Safari5.1-6 */
            background: linear-gradient(to bottom, #fcfff4 0%, #e9e9ce 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#fcfff4', endColorstr='#e9e9ce', GradientType=0); /* IE6-9 */
        }

        .dd3-content:hover {
            background: #fff;
        }

        .dd-dragel > .dd3-item > .dd3-content {
            margin: 0;
        }

        .dd3-item > button {
            margin-left: 35px;
        }

        .dd3-handle {
            position: absolute;
            margin: 0;
            left: 0;
            top: 0;
            cursor: all-scroll;
            width: 30px;
            text-indent: 100%;
            white-space: nowrap;
            overflow: hidden;
            border: 1px solid #3276B1;
            background: #428BCA;
            height: 30px;
            box-shadow: 1px 1px 0 rgba(255, 255, 255, 0.2) inset;
        }

        .dd3-handle:before {
            content: '=';
            display: block;
            position: absolute;
            left: 0;
            top: 2px;
            width: 100%;
            text-align: center;
            text-indent: 0;
            color: #fff;
            font-size: 12px;
            font-weight: normal;
        }

        .dd3-handle:hover {
            background: #4E9DFF;
        }
    </style>
    @include('layout.breadcrumb',['data' => [
        ['name' => "Menus",'url'=> '','active' => 'yes']
    ]
    ])
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        @include('app.menus.tabs',['user_group_id' => $user_group_id])
                    </div>
                    <div class="panel-body">
                        <div class="tab-content">
                            <div id="root" class="tab-pane fade in active">
                                <div class="col-md-12">
                                    <div class="card" id="viewPort">
                                        <div class="card-block">
                                            <div class="row m-t-20">
                                                <div class="col-md-6">
                                                    <div class="panel panel-danger">
                                                        <div class="panel-body">
                                                            <div id="list2" class="dd">
                                                                <ol class="dd-list">
                                                                    @foreach ($menus as $menu)
                                                                        <li data-id="{{$menu['id']}}"
                                                                            class="dd-item dd3-item">
                                                                            <div class="dd-handle dd3-handle"></div>
                                                                            <div class="dd3-content">{{$menu['name']}}
                                                                                <span class="pull-right">
						<a href="{{ secure_url('menus/'.$menu['id'].'?template='.$user_group_id)}}"><i
                                    class="fa fa-edit "></i></a></span>
                                                                            </div>
                                                                            @if(count($menu['childs']) > 0)
                                                                                <ol class="dd-list" style="">
                                                                                    @foreach ($menu['childs'] as $menu2)
                                                                                        <li data-id="{{$menu2['id']}}"
                                                                                            class="dd-item dd3-item">
                                                                                            <div class="dd-handle dd3-handle"></div>
                                                                                            <div class="dd3-content">{{$menu2['name']}}
                                                                                                <span class="pull-right">
									<a href="{{ secure_url('/menus/'.$menu2['id'].'?template='.$user_group_id)}}"><i
                                                class="fa fa-edit"></i></a></span>
                                                                                            </div>
                                                                                            @if(count($menu2['childs']) > 0)
                                                                                                <ol class="dd-list"
                                                                                                    style="">
                                                                                                    @foreach($menu2['childs'] as $menu3)
                                                                                                        <li data-id="{{$menu3['id']}}"
                                                                                                            class="dd-item dd3-item">
                                                                                                            <div class="dd-handle dd3-handle"></div>
                                                                                                            <div class="dd3-content">{{ $menu3['name'] }}
                                                                                                                <span class="pull-right">
												<a href="{{ secure_url('menus/'.$menu3['id'].'?template='.$user_group_id)}}"><i
                                                            class="fa fa-edit"></i></a>
												</span>
                                                                                                            </div>
                                                                                                        </li>
                                                                                                    @endforeach
                                                                                                </ol>
                                                                                            @endif
                                                                                        </li>
                                                                                    @endforeach
                                                                                </ol>
                                                                            @endif
                                                                        </li>
                                                                    @endforeach
                                                                </ol>
                                                            </div>
                                                            <form class="" method="POST"
                                                                  action="{{ secure_url('menu/re-order') }}">
                                                                <input type="hidden" name="reorder" id="reorder"
                                                                       value=""/>
                                                                <input type="hidden" name="user_group_id" value="{{ $user_group_id }}">
                                                                {{ csrf_field() }}
                                                                <br><br>
                                                                <button type="submit"
                                                                        class="btn btn-theme pull-right">{{ trans('common.lbl_reorder_menu') }}
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="panel panel-primary">
                                                        <div class="panel-heading">
                                                            <h5 class=""> @if($row['id'] =='')
                                                                    <i class="fa fa-plus"></i>
                                                                    {{ trans('menu.lbl_menu_create') }}
                                                                @else
                                                                    <i class="fa fa-pencil"></i>
                                                                    {{ trans('menu.lbl_menu_edit') }}
                                                                @endif</h5>
                                                        </div>
                                                        <!-- /.box-header -->
                                                        <div class="panel-body">
                                                            <form class="form-horizontal"
                                                                  action="{{ secure_url('menu/save') }}" method="POST">
                                                                {{ csrf_field() }}
                                                                <input type="hidden" name="ordering" value="{{ $row['ordering'] }}">
                                                                <input type="hidden" name="parent_id" value="{{ $row['parent_id'] }}">
                                                                <input type="hidden" name="group_id" value="{{ $user_group_id }}">
                                                                <div class=" ">
                                                                    <input type="hidden" name="id" id="id"
                                                                           value="{{ $row['id'] }}"/>
                                                                    <div class="form-group row {{ $errors->has('name') ? ' has-error' : '' }}">
                                                                        <label for="ipt"
                                                                               class=" control-label col-md-4">{{ trans('menu.fr_mtitle') }}  </label>
                                                                        <div class="col-md-8">

                                                                            <input type="text" class="form-control"
                                                                                   name="name"
                                                                                   value="{{ old('name',$row['name']) }}"
                                                                                   id="name" tabindex="1">
                                                                            @if(ENABLE_MULTI_LANG ==1)
                                                                                <?php $lang = [
                                                                                    ['folder' => 'en', 'name' => 'English'],
                                                                                    ['folder' => 'fr', 'name' => 'French'],
                                                                                    // Add more languages as needed
                                                                                ];
                                                                                foreach($lang as $l) {
                                                                                if($l['folder'] != 'en') {
                                                                                ?>
                                                                                @if(isset($trans_lang['title'][$l['folder']]))
                                                                                    <div class="input-group input-group-sm"
                                                                                         style="margin:5px 0 !important;">
                                                                                        <input name="language_title[<?php echo $l['folder'];?>]"
                                                                                               type="text"
                                                                                               class="form-control"
                                                                                               placeholder="Title for <?php echo $l['name'];?>"
                                                                                               value="<?php echo(isset($trans_lang['title'][$l['folder']]) ? $trans_lang['title'][$l['folder']] : '');?>"/>
                                                                                        <span class="input-group-addon xlick bg-default btn-sm "><?php echo strtoupper($l['folder']);?></span>
                                                                                    </div>
                                                                                @else
                                                                                    <div class="input-group input-group-sm"
                                                                                         style="margin:5px 0 !important;">
                                                                                        <input name="language_title[<?php echo $l['folder'];?>]"
                                                                                               type="text"
                                                                                               class="form-control"
                                                                                               placeholder="Title for <?php echo $l['name'];?>"
                                                                                               value=""/>
                                                                                        <span class="input-group-addon xlick bg-default btn-sm "><?php echo strtoupper($l['folder']);?></span>
                                                                                    </div>

                                                                                @endif
                                                                                <?php
                                                                                }

                                                                                }
                                                                                ?>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group  row ext-link">
                                                                        <label for="url"
                                                                               class="control-label col-md-4"> {{ trans('menu.fr_murl') }}  </label>
                                                                        <div class="col-md-8">
                                                                            <input type="text" id="url" name="url"
                                                                                   class="form-control"
                                                                                   placeholder="url" tabindex="2"
                                                                                   value="{{ $row['url'] }}">
                                                                            <small class="text-muted hide"
                                                                                   id="sub-url"></small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group  row ext-link">
                                                                        <label for="menu_icon"
                                                                               class="control-label col-md-4"> {{ trans('menu.fr_micon') }} </label>
                                                                        <div class="col-md-8 ">
                                                                            <div class="input-group">
                                                                                <input type="text" id="menu_icon" tabindex="3"
                                                                                       name="menu_icon"
                                                                                       class="form-control"
                                                                                       placeholder=""
                                                                                       value="{{ $row['icon'] }}">
                                                                                <span class="input-group-addon"
                                                                                      id="id-picker"><i
                                                                                            class="fa fa-shield"></i></span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label for="ipt"
                                                                               class="control-label col-md-4"> {{ trans('menu.fr_mposition') }}  </label>

                                                                        <div class="col-md-8">
                                                                            <label class="radio-inline">
                                                                                <input tabindex="4" class="remember"
                                                                                       name="position" type="radio"
                                                                                       value="sidebar"
                                                                                       @if($row['position'] == 'sidebar') checked @endif >&nbsp;{{ trans('menu.tab_sidemenu') }}
                                                                            </label>

                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row {{ $errors->has('is_active') ? ' has-error' : '' }}">
                                                                        <label for="ipt"
                                                                               class=" control-label col-md-4"> {{ trans('menu.fr_mstatus') }}  </label>

                                                                        <div class="col-md-8">
                                                                            <label class="radio-inline">
                                                                                <input type="radio" class="minimal-red"
                                                                                       name="is_active" value="1" tabindex="5"
                                                                                       @if($row['status']=='1' ) checked="checked" @endif />&nbsp;{{ trans('menu.fr_mactive') }}
                                                                            </label>
                                                                            <label class="radio-inline">
                                                                                <input type="radio" class="minimal-red"
                                                                                       name="is_active" value="0" tabindex="6"
                                                                                       @if($row['status']=='0' ) checked="checked" @endif />&nbsp;{{ trans('menu.fr_minactive') }}
                                                                            </label>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="col-md-4 text-right">&nbsp;</label>

                                                                        <div class="col-md-8">
                                                                            <button type="submit" tabindex="7"
                                                                                    class="btn btn-theme "><i
                                                                                        class="fa fa-save"></i>&nbsp;{{ trans('common.btn_save') }}
                                                                            </button>
                                                                            @if($row['id'] !='')
                                                                                <button type="button"
                                                                                        onclick="AppConfirmDelete('{{ secure_url('menu/remove/'.$row['id'].'?template='.$user_group_id)}}','{{ trans('common.lbl_remove') . " ". $row['name'] }}','{{ trans('common.ask_remove') }}');return false;"
                                                                                        class="btn btn-danger "><i
                                                                                            class="fa fa-times"></i>&nbsp;{{ trans('common.btn_delete') }}
                                                                                </button>
                                                                            @endif
                                                                        </div>

                                                                    </div>

                                                                </div>
                                                            </form>


                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="{{ secure_asset('vendor/common/jquery.nestable.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.dd').nestable();
            update_order('#list2', "#reorder");

            $("#url").on('keyup', function () {
                if ($("#sub-url").hasClass('hide')) {
                    $("#sub-url").removeClass('hide');
                    $("#sub-url").html("{{ URL::to('/') }}/" + $(this).val());
                } else {
                    $("#sub-url").html("{{ URL::to('/') }}/" + $(this).val());
                }
            });

            $('#list2').on('change', function () {
                var out = $('#list2').nestable('serialize');
                $('#reorder').val(JSON.stringify(out));

            });
        });
        function update_order(selector, sel2) {

            var out = $(selector).nestable('serialize');
            $(sel2).val(JSON.stringify(out));

        }
    </script>
@endsection