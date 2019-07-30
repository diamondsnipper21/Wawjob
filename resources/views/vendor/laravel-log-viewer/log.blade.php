<?php
/**
* Freelancer Proposals Page on Super Admin
*
* @author KCG
* @since July 13, 2017
* @version 1.0
*/

use iJobDesk\Models\User;

?>
@extends('layouts/admin/super')

@section('content')

<div id="log_viewer">
    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-cogs font-green-sharp"></i>
                <span class="caption-subject font-green-sharp bold">Log Viewer</span>
            </div>
        </div><!-- .portlet-title -->
        <div class="portlet-body">
            <div class="row">
                <div class="col-md-2 sidebar">
                    <div class="list-group">
                    @foreach($files as $file)
                        <a href="?l={{ \Illuminate\Support\Facades\Crypt::encrypt($file) }}" class="list-group-item @if ($current_file == $file) llv-active @endif">{{$file}}</a>
                    @endforeach
                    </div>
                </div>
                <div class="col-md-10 table-container">
                    @if ($logs === null)
                    <div>Log file >50M, please download it.</div>
                    @else
                    <table id="table-log" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Level</th>
                                <th>Context</th>
                                <th>Date</th>
                                <th>Content</th>
                            </tr>
                        </thead>
                        <tbody>

                        @foreach($logs as $key => $log)
                        <tr data-display="stack{{{$key}}}">
                            <td class="text-{{{$log['level_class']}}}"><span class="fa fa-{{{$log['level_img']}}}"
                                                       aria-hidden="true"></span>&nbsp;{{$log['level']}}</td>
                            <td class="text">{{$log['context']}}</td>
                            <td class="date">{{{$log['date']}}}</td>
                            <td class="text">
                            @if ($log['stack']) <button type="button" class="float-right expand btn btn-outline-dark btn-sm mb-2 ml-2"
                               data-display="stack{{{$key}}}"><span
                            class="fa fa-search"></span></button>@endif
                            {{{$log['text']}}}
                            @if (isset($log['in_file'])) <br/>{{{$log['in_file']}}}@endif
                            @if ($log['stack'])
                            <div class="stack" id="stack{{{$key}}}" style="display: none; white-space: pre-wrap;">{{{ trim($log['stack']) }}}
                            </div>
                            @endif
                            </td>
                        </tr>
                        @endforeach

                        </tbody>
                    </table>
                    @endif
                    <div class="p-3">
                        @if($current_file)
                        <a href="?dl={{ \Illuminate\Support\Facades\Crypt::encrypt($current_file) }}"><span class="fa fa-download"></span>Download file</a>
                        -
                        <a id="delete-log" href="?del={{ \Illuminate\Support\Facades\Crypt::encrypt($current_file) }}"><span class="fa fa-trash"></span> Delete file</a>
                        @if(count($files) > 1)
                        -
                        <a id="delete-all-log" href="?delall=true"><span class="fa fa-trash"></span> Delete all files</a>
                        @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
