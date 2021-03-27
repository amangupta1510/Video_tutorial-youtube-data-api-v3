@extends('layout/admin_dashboard')
@extends('layout/details')
@section('popup')
<script src="{{asset('js/ckeditors/ckeditor.js')}}"></script>
<link href="{{ asset('adminsa/bootstrap/css/bootstrap-datetimepicker.min.css')}}" rel="stylesheet" media="screen">
<?php $date = new \DateTime();
$timer=date_format($date, 'Y-m-d H:i:s');?>
<link rel="stylesheet" type="text/css" href="{{ asset('css/videocss_mobile.css') }}">
<div id="limiter" class="limiter col-md-12" style="display: none; padding-right: 0rem;padding-left: 0rem;">
    <div id="videoContainer" class="col-md-12 px-0">
        <div class="embed-responsive embed-responsive-4by3" id="video">
            <div class="overlay">
                <div style="position: absolute;width: 100%;height: 60px; background-color: #00e0fd;">
                    <ul class="online_student" onclick="closeroom()">
                        <li><img class="icon" src="{{ asset('img/back.png') }}" width="30"></li>
                    </ul>
                    <ul class="online_student" style="background: transparent;width: auto;font-size: 20px; color: #30180fc4;">
                        <li><b>Lecture </b></li>
                    </ul>
                </div>
            </div>
            <style type="text/css">
            .online_student {
                display: inline-flex;
                list-style: none;
                width: auto;
                padding: 10px;
                margin: 5px;
                border: 1px #4d4d4d;
                z-index: 99999999;
                max-height: 80vh;
                overflow-y: scroll;
                background: #2e2e2e;
            }

            .online_student:hover li ul {
                display: block;
            }

            .online_student:hover li ul li:hover {
                background-color: #5e5e5e;
                width: auto;
            }

            .online_student li ul {
                display: none;
            }

            </style>
            <div id="video-placeholder"></div>
            <div id="controls">
                <div onclick="changeprogress(event)" class="ProgressContainer">
                    <div class="progressBar">
                        <div id="progress-bar" class="progress" style="position: absolute; z-index: 22"></div>
                        <div id="progress-bar1" class="progress1" style="position: absolute;z-index: 10"></div>
                    </div>
                </div>
                <div class="playbtn">
                    <div id="play" class="playButton"><img src="{{asset('img/play.png')}}" style="width: 80%;padding: 10%;"></div>
                    <div id="pause" class="playPause" style="display: none;"><img src="{{asset('img/pause.png')}}" style="width: 80%;padding: 10%;"></div>
                </div>
                <div id="current-time" class="timer intialTime">0:00 / 00:00</div>
                <div class="volume">
                    <div id="mute-toggle"><img src="{{asset('img/volume.png')}}" class="icon muteno"><img src="{{asset('img/mute.png')}}" class="icon muteyes" style="display: none;"></div>
                    <div onclick="changevol(event)" class="intensityBar">
                        <div class="intensity"></div>
                    </div>
                </div>
                <select id="speed" class="speedicon">
                    <option value="0.25">0.25x</option>
                    <option value="0.5">0.5x</option>
                    <option value="1" selected="selected">1x</option>
                    <option value="1.5">1.5x</option>
                    <option value="2">2x</option>
                </select>
                <select id="quality" class="speedicon1">
                    <option value="small">240p</option>
                    <option value="medium">360p</option>
                    <option value="large" selected="selected">480p</option>
                    <option value="hd720">720p</option>
                    <option value="hd1080">1080p</option>
                    <option value="highres">highest</option>
                </select>
            </div>
        </div>
    </div>
</div>
</div>
<style type="text/css">
.msg-left {
    font-size: 11px;
    max-width: 80%;
    margin-bottom: 7px;
    float: left;
    clear: both;
}

.msg-left a,
.msg-right a {
    float: right;
}

.msg-left b,
.msg-right b {
    font-size: 10px;
}

.msg-right {
    font-size: 11px;
    max-width: 80%;
    margin-bottom: 7px;
    float: right;
    clear: both;

}

</style>
@endsection
@section('analysis')
<div class="d-sm-flex justify-content-between align-items-center mb-4 pt-2">
    @if($type=='subject')
    <h3 class="text-dark mb-0">Lecture Subjects&nbsp;<a class="btn btn-success" id="add-button_subject" style="padding: 6px 8px;font-size: 13px;">Add Subject&nbsp;</a></h3>
</div>
<div class="row">
    <?php $no =0;$ar=array(); $ar[1]="danger";$ar[2]="success";$ar[3]="primary";$ar[4]="warning";$ar[0]="info"; ?>
    @foreach($users as $user)
    <?php $no++; $n=$no % 5;$lable=""; ?>
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card shadow border-left-{{$ar[$n]}}">
            <div class="card-body">
                <div class="row align-items-center no-gutters">
                    <div class="col mr-2">
                        <div class="text-capitalize text-{{$ar[$n]}} font-weight-bold text-xs"><span>Subject</span></div>
                        <div class="text-dark font-weight-bold mb-0 text-xs"><span></span><a style="font-size: 14px;">{{$user->name}}</a></div>
                        <div class="text-dark mt-1  mb-2 text-xs font-weight-bold"><span></span>
                            <a style="font-size: 14px; max-width: 70%; float: left;"><i class="glyphicon glyphicon-trash pt-1 delete_subject" data-id="{{$user->id}}" data-name="{{$user->name}}" style="color: #ff5c33;font-size: 15px;"></i>&nbsp;&nbsp;&nbsp;&nbsp;<i class="glyphicon glyphicon-pencil edit_subject" data-id="{{$user->id}}" data-name="{{$user->name}}" style="color: #ff9933;font-size: 15px;"></i></a><a style="float: right; font-size: 14px; padding: 3px 6px; color: #fff;" class="btn btn-success details" href="{{ route('admin-lectures',['fid'=>$user->id,'fname'=>$user->name]) }}">Open&nbsp;<i style="font-size: 11px;">({{$user->count}})</i></a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
@if($type=='topic')
<h3 class="text-dark mb-0">Lecture Topics ({{app('request')->input('fname')}})&nbsp;<a class="btn btn-success" id="add-button_topic" style="padding: 6px 8px;font-size: 13px;">Add Topic&nbsp;</a></h3>
</div>
<div class="row">
    <?php $no =0;$ar=array(); $ar[1]="danger";$ar[2]="success";$ar[3]="primary";$ar[4]="warning";$ar[0]="info"; ?>
    @foreach($users as $user)
    <?php $no++; $n=$no % 5;$lable=""; ?>
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card shadow border-left-{{$ar[$n]}}">
            <div class="card-body">
                <div class="row align-items-center no-gutters">
                    <div class="col mr-2">
                        <div class="text-capitalize text-{{$ar[$n]}} font-weight-bold text-xs"><span>Topic</span></div>
                        <div class="text-dark font-weight-bold mb-0 text-xs"><span></span><a style="font-size: 14px;">{{$user->name}}</a></div>
                        <div class="text-dark mt-1  mb-2 text-xs font-weight-bold"><span></span>
                            <a style="font-size: 14px; max-width: 70%; float: left;"><i class="glyphicon glyphicon-trash pt-1 delete_topic" data-id="{{$user->id}}" data-name="{{$user->name}}" style="color: #ff5c33;font-size: 15px;"></i>&nbsp;&nbsp;&nbsp;&nbsp;<i class="glyphicon glyphicon-pencil edit_topic" data-id="{{$user->id}}" data-name="{{$user->name}}" style="color: #ff9933;font-size: 15px;"></i> </a><a style="float: right; font-size: 14px; padding: 3px 8px; color: #fff;" class="btn btn-success details" href="{{ route('admin-lectures',['fid'=>$user->folder_id,'fname'=>$user->folder_name,'sfid'=>$user->id,'sfname'=>$user->name]) }}">Open&nbsp;<i style="font-size: 11px;">({{$user->count}})</i></a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
@if($type=='lecture')
<h3 class="text-dark mb-0">Lectures ({{app('request')->input('sfname')}})&nbsp;<a class="btn btn-success" id="add-button_lecture" style="padding: 6px 8px;font-size: 13px;">Add Lecture&nbsp;</a></h3>
</div>
<div class="row">
    <?php $no =0;$ar=array(); $ar[1]="danger";$ar[2]="success";$ar[3]="primary";$ar[4]="warning";$ar[0]="info"; ?>
    @foreach($users as $user)
    <?php $no++; $n=$no % 5;$lable=""; ?>
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card shadow border-left-{{$ar[$n]}}">
            <div class="card-body">
                <div class="row align-items-center no-gutters">
                    <div class="col mr-2">
                        <div class="text-capitalize text-{{$ar[$n]}} font-weight-bold text-xs"><span>Lecture</span></div>
                        <div class="text-dark font-weight-bold mb-0 text-xs"><span></span><a style="font-size: 14px;">{{$user->title}}</a></div>
                        <div class="text-dark mb-3 text-xs lec_des" style="font-size: 11px;" data-data="{{$user->description}}"></div>
                        <div class="text-dark mt-1 mb-2 text-xs font-weight-bold"><span></span>
                            <a style="font-size: 13px; max-width: 75%; float: left;"><a style="font-size: 14px; padding: 1px 4px; background: #eee; color: #ff9933;" class="btn delete_lecture" data-id="{{$user->id}}" data-title="{{$user->title}}"><i class="glyphicon glyphicon-trash" style="color: #ff5c33;"></i></a>&nbsp;&nbsp;<a style="font-size: 14px; padding: 1px 4px; background: #eee; color: #ff9933;" class="btn edit_lecture" data-id="{{$user->id}}" data-title="{{$user->title}}" data-video_code="{{$user->video_code}}" data-description="{{$user->description}}"><i class="glyphicon glyphicon-pencil" style="color: #ff9933;"></i></a>&nbsp;&nbsp;<a style="font-size:11px; padding: 3px 4px; color: #fff;" class="btn btn-primary" id="add-button_lecture_link" data-id="{{$user->id}}">Publish</a>&nbsp;&nbsp;<a style="font-size:11px; padding: 3px 8px; color: #fff;" class="btn btn-info lecture_link_list" data-id="{{$user->id}}">List&nbsp;<i style="font-size: 12px;">({{$user->count}})</i></a></a><a style="float: right; font-size: 14px; padding: 3px 12px; color: #fff;" class="btn btn-danger details openroom" data-id="{{$user->id}}">Play &nbsp;<i class="fa fa-play-circle"></i></a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    <script type="text/javascript">
    function desc() {
        $('.lec_des').each(function(e) {
            $(this).html($(this).data("data"));

        });
    }
    desc();

    </script>
</div>
@endif
</div>
<style type="text/css">
.card-body {
    padding: .25rem 1.15rem;
}

.slidecontainer {
    width: 100%;
}

.slider {
    -webkit-appearance: none;
    width: 67%;
    height: 8px;
    padding: 0;
    border-radius: 5px;
    background: #d3d3d3;
    outline: none;
    opacity: 0.7;
    -webkit-transition: .2s;
    transition: opacity .2s;
}

.slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #4CAF50;
    cursor: pointer;
}

.slider::-moz-range-thumb {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #4CAF50;
    cursor: pointer;
}

.description {
    font-size: 12px;
}

.description p {
    margin: 0;
}

</style>
@endsection
@section('js')
<script type="text/javascript" src="{{ asset('adminsa/bootstrap/js/bootstrap-datetimepicker.js') }}" charset="UTF-8"></script>
<script type="text/javascript" src="{{ asset('adminsa/bootstrap/js/bootstrap-datetimepicker.fr.js') }}" charset="UTF-8"></script>
<script type="text/javascript">
$("body").delegate(".openroom", "click", function() {
    var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
    if (isMobile) {
        // var img ="{{ route('admin-mobile_video_lecture',['id'=>':year']) }}";
        // var img = img.replace('%3Ayear',$(this).data('id'));
        //  window.location.href=img;
        fullscreen($(this).data('id'));
    } else {
        var img = "{{ route('admin-video_lecture',['id'=>':year']) }}";
        var img = img.replace('%3Ayear', $(this).data('id'));
        window.location.href = img;
    }
});

$("body").delegate("#add-button_subject", "click", function() {
    $(".spinner").hide();
    $('.socket_title').text('Add Subject');
    $('.socket_body').empty().html('@csrf<input type="text" name="subject_name" id="subject_name"><br><a style="color:#fff;" class="btn btn-success add_subject" style="margin:10px;" >Add Subject</a>');
    $("#socket").show("closed");
});
$("body").delegate(".add_subject", "click", function() {
    var name = $("#subject_name").val();
    if (name == '') {
        return false;
    }
    $.ajax({
        type: 'POST',
        url: '{{ route('
        admin - add_lecture_folder ') }}',
        data: {
            '_token': $('input[name=_token]').val(),
            'name': name,
        },
        success: function(data) {
            $("#socket").hide(500);
            location.reload();
        },
    });
});

$("body").delegate("#add-button_topic", "click", function() {
    $(".spinner").hide();
    $('.socket_title').text('Add Topic');
    $('.socket_body').empty().html('@csrf<input type="text" name="topic_name" id="topic_name"><br><a style="color:#fff;" class="btn btn-success add_topic" style="margin:10px;" >Add Topic</a>');
    $("#socket").show("closed");
});
$("body").delegate(".add_topic", "click", function() {
    var name = $("#topic_name").val();
    if (name == '') {
        return false;
    }
    $.ajax({
        type: 'POST',
        url: '{{ route('
        admin - add_lecture_subfolder ') }}',
        data: {
            '_token': $('input[name=_token]').val(),
            'folder_id': '{{app('
            request ')->input('
            fid ')}}',
            'folder_name': '{{app('
            request ')->input('
            fname ')}}',
            'name': name,
        },
        success: function(data) {
            $("#socket").hide(500);
            location.reload();
        },
    });
});
$("body").delegate("#add-button_lecture", "click", function() {
    $(".spinner").hide();
    $('.socket_title').text('Add Lecture');
    $('.socket_body').empty().html('@csrf<input type="text" name="lecture_title" id="lecture_title" placeholder="Title"><input type="text" name="lecture_video_code" id="lecture_video_code"  placeholder="Video Link"><textarea rows="4" cols="50" style="border:1px;width:100%;background:#eee;" type="text" name="lecture_description" id="lecture_description"  placeholder="Description"></textarea><br><a style="color:#fff;" class="btn btn-success add_lecture" style="margin:10px;" >Add Lecture</a>');
    CKEDITOR.replace('lecture_description', {
        filebrowserUploadUrl: "{{route('admin-ckeditor_upload', ['_token' => csrf_token() ])}}",
        filebrowserUploadMethod: 'form'
    });
    $("#socket").show("closed");
});
$("body").delegate(".add_lecture", "click", function() {
    for (instance in CKEDITOR.instances) {
        CKEDITOR.instances[instance].updateElement();
    }
    var title = $("#lecture_title").val();
    var video_code = $("#lecture_video_code").val();
    var description = $("#lecture_description").val();
    if (title == '' || video_code == '') {
        return false;
    }
    $.ajax({
        type: 'POST',
        url: '{{ route('
        admin - add_lecture ') }}',
        data: {
            '_token': $('input[name=_token]').val(),
            'folder_id': '{{app('
            request ')->input('
            fid ')}}',
            'folder_name': '{{app('
            request ')->input('
            fname ')}}',
            'subfolder_id': '{{app('
            request ')->input('
            sfid ')}}',
            'subfolder_name': '{{app('
            request ')->input('
            sfname ')}}',
            'title': title,
            'video_code': video_code,
            'description': description
        },
        success: function(data) {
            $("#socket").hide(500);
            location.reload();
        },
    });
});

$("body").delegate("#add-button_lecture_link", "click", function() {
    var id = $(this).data('id');
    $(".spinner").hide();
    $('.socket_title').text('Publish Lecture');
    $('.socket_body').empty().html('@csrf<div><select id="class" class="form-control" name="class" ><option value="">Class</option><option value="8th">8th</option><option value="9th">9th</option><option value="10th">10th</option> <option value="11th">11th</option> <option value="12th">12th</option> <option value="Repeater">Repeater</option></select></div><div ><select id="course"  class="form-control" name="course" ><option value="">Course</option><option value="Foundation">Foundation</option><option value="JEE Main">JEE Main</option><option value="JEE (Main + Advance)">JEE (Main + Advance)</option> <option value="NEET">NEET</option> <option value="NEET + AIIMS">NEET + AIIMS</option> <option value="MHT-CET">MHT-CET</option><option value="Classroom Test">Classroom Test</option></select> </div><div ><select id="coursetype" class="form-control" name="coursetype"> <option value="">Course Type</option><option value="Classroom Course">Classroom Course</option><option value="Crash Course">Crash Course</option><option value="Distance Learning">Distance Learning</option></select> </div><div  style="display: flex;"> <label>Group</label> <input style="display: none;" type="radio" name="radio" value="" checked="true"></input>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A<input type="radio" name="radio" value="A">  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;B<input type="radio" name="radio" value="B">  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;C<input type="radio" name="radio" value="C">  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;D<input type="radio" name="radio" value="D">  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;E<input type="radio" name="radio" value="E">  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;F<input type="radio" name="radio" value="F"></div><br><a style="color:#fff;" class="btn btn-success add_lecture_link" style="margin:10px;" data-id="' + id + '" >Publish Lecture</a>');
    $("#socket").show("closed");
});
$("body").delegate(".add_lecture_link", "click", function() {
    if ($("#class").val() == '' || $("#course").val() == '' || $("#coursetype").val() == '' || $("input[name='radio']:checked").val() == '') {
        return false;
    }
    var title = $("#lecture_title").val();
    var video_code = $("#lecture_video_code").val();
    var description = $("#lecture_description").val();
    $.ajax({
        type: 'POST',
        url: '{{ route('
        admin - add_lecture_link ') }}',
        data: {
            '_token': $('input[name=_token]').val(),
            'lecture_id': $(this).data('id'),
            'class': $("#class").val(),
            'course': $("#course").val(),
            'coursetype': $("#coursetype").val(),
            'group': $("input[name='radio']:checked").val()

        },
        success: function(data) {
            $("#socket").hide(500);
            location.reload();
        },
    });
});

//-------------------------------------------------------edit section---------------------------------------------------


$("body").delegate(".lecture_link_list", "click", function() {
    $(".spinner").hide();
    var img = "{{ route('admin-lecture_links') }}?id=" + $(this).data('id');
    $('.socket_title').text('Published Lectures');
    $('.socket_body').empty().html("<br>");
    $.get(img, function(data) {
        for (var i = 0; i < data.length; i++) {
            var j = parseInt(i) + parseInt(1);
            var j = j > 9 ? j : '0' + j;
            $('.socket_body').append('<tr><td style="padding-right:5px;font-size: 13px;"><span class="font-weight-bold">' + j + '</span></td><td style="padding-right:5px;font-size: 13px;"><span class="font-weight-bold">' + data[i].classid + '|' + data[i].courseid + '|' + data[i].coursetypeid + '|' + data[i].groupid + '</span></td><td style="padding-right:5px;"><a style="font-size: 11px; padding: 3px 6px; color: #fff;" class="btn btn-info edit_lecture_link" data-id="' + data[i].id + '" data-class="' + data[i].classid + '"data-course="' + data[i].courseid + '"data-coursetype="' + data[i].coursetypeid + '"data-group="' + data[i].groupid + '" >Edit</a></td><td><a style="font-size: 11px; padding: 3px 6px; color: #fff;" class="btn btn-danger delete_lecture_link" data-id="' + data[i].id + '">Delete</a></td></tr>');
        }
    })
    $("#socket").show("closed");
});

$("body").delegate(".edit_subject", "click", function() {
    $(".spinner").hide();
    $('.socket_title').text('Edit Subject');
    $('.socket_body').empty().html('@csrf<input type="text" name="subject_name" id="subject_name" value="' + $(this).data('name') + '"><br><a style="color:#fff;" class="btn btn-success edit_subject_confirm" data-id="' + $(this).data('id') + '" style="margin:10px;" >Edit Subject</a>');
    $("#socket").show("closed");
});
$("body").delegate(".edit_subject_confirm", "click", function() {
    var name = $("#subject_name").val();
    if (name == '') {
        return false;
    }
    $.ajax({
        type: 'POST',
        url: '{{ route('
        admin - edit_lecture_folder ') }}',
        data: {
            '_token': $('input[name=_token]').val(),
            'name': name,
            'id': $(this).data('id')
        },
        success: function(data) {
            $("#socket").hide(500);
            location.reload();
        },
    });
});

$("body").delegate(".edit_topic", "click", function() {
    $(".spinner").hide();
    $('.socket_title').text('Edit Topic');
    $('.socket_body').empty().html('@csrf<input type="text" name="topic_name" id="topic_name" value="' + $(this).data('name') + '"><br><a style="color:#fff;" class="btn btn-success edit_topic_confirm" style="margin:10px;" data-id="' + $(this).data('id') + '">Edit Topic</a>');
    $("#socket").show("closed");
});
$("body").delegate(".edit_topic_confirm", "click", function() {
    var name = $("#topic_name").val();
    if (name == '') {
        return false;
    }
    $.ajax({
        type: 'POST',
        url: '{{ route('
        admin - edit_lecture_subfolder ') }}',
        data: {
            '_token': $('input[name=_token]').val(),
            'name': name,
            'id': $(this).data('id')
        },
        success: function(data) {
            $("#socket").hide(500);
            location.reload();
        },
    });
});
$("body").delegate(".edit_lecture", "click", function() {
    $(".spinner").hide();
    $('.socket_title').text('Edit Lecture');
    $('.socket_body').empty().html("");
    $('.socket_body').empty().html('@csrf<input type="text" name="lecture_title" id="lecture_title" placeholder="Title" value="' + $(this).data('title') + '"><input type="text" name="lecture_video_code" id="lecture_video_code"  placeholder="Video Link"value="' + $(this).data('video_code') + '"><textarea rows="4" cols="50" style="border:1px;width:100%;background:#eee;" type="text" name="lecture_description" id="lecture_description"  placeholder="Description">' + $(this).data('description') + '</textarea><br><a style="color:#fff;" class="btn btn-success edit_lecture_confirm" style="margin:10px;" data-id="' + $(this).data('id') + '">Edit Lecture</a>');
    CKEDITOR.replace('lecture_description', {
        filebrowserUploadUrl: "{{route('admin-ckeditor_upload', ['_token' => csrf_token() ])}}",
        filebrowserUploadMethod: 'form'
    });
    CKEDITOR.instances['lecture_description'].setData($(this).data('description'));
    $("#socket").show("closed");
});
$("body").delegate(".edit_lecture_confirm", "click", function() {
    for (instance in CKEDITOR.instances) {
        CKEDITOR.instances[instance].updateElement();
    }
    var title = $("#lecture_title").val();
    var video_code = $("#lecture_video_code").val();
    var description = $("#lecture_description").val();
    if (title == '' || video_code == '') {
        return false;
    }
    $.ajax({
        type: 'POST',
        url: '{{ route('
        admin - edit_lecture ') }}',
        data: {
            '_token': $('input[name=_token]').val(),
            'id': $(this).data('id'),
            'title': title,
            'video_code': video_code,
            'description': description
        },
        success: function(data) {
            $("#socket").hide(500);
            location.reload();
        },
    });
});

$("body").delegate(".edit_lecture_link", "click", function() {
    var id = $(this).data('id');
    $(".spinner").hide();
    $('.socket_title').text('Edit Publish Lecture');
    $('.socket_body').empty().html("");
    $('.socket_body').empty().html('@csrf<div><select id="class" class="form-control" name="class"><option value="">Class</option><option value="8th">8th</option><option value="9th">9th</option><option value="10th">10th</option> <option value="11th">11th</option> <option value="12th">12th</option> <option value="Repeater">Repeater</option></select></div><div ><select id="course"  class="form-control" name="course" ><option value="">Course</option><option value="Foundation">Foundation</option><option value="JEE Main">JEE Main</option><option value="JEE (Main + Advance)">JEE (Main + Advance)</option> <option value="NEET">NEET</option> <option value="NEET + AIIMS">NEET + AIIMS</option> <option value="MHT-CET">MHT-CET</option><option value="Classroom Test">Classroom Test</option></select> </div><div ><select id="coursetype" class="form-control" name="coursetype"> <option value="">Course Type</option><option value="Classroom Course">Classroom Course</option><option value="Crash Course">Crash Course</option><option value="Distance Learning">Distance Learning</option></select> </div><div  style="display: flex;"> <label>Group</label> <input style="display: none;" type="radio" name="radio" value="" checked="true"></input>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A<input type="radio" name="radio" value="A">  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;B<input type="radio" name="radio" value="B">  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;C<input type="radio" name="radio" value="C">  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;D<input type="radio" name="radio" value="D">  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;E<input type="radio" name="radio" value="E">  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;F<input type="radio" name="radio" value="F"></div><br><a style="color:#fff;" class="btn btn-success edit_lecture_link_confirm" style="margin:10px;" data-id="' + id + '" >Save Published Lecture</a>');
    $("#socket").show("closed");
    $("input[name=radio][value=" + $(this).data('group') + "]").prop('checked', true);
    $('#class').val($(this).data('class'));
    $('#course').val($(this).data('course'));
    $('#coursetype').val($(this).data('coursetype'));

});
$("body").delegate(".edit_lecture_link_confirm", "click", function() {
    if ($("#class").val() == '' || $("#course").val() == '' || $("#coursetype").val() == '' || $("input[name='radio']:checked").val() == '') {
        return false;
    }
    var title = $("#lecture_title").val();
    var video_code = $("#lecture_video_code").val();
    var description = $("#lecture_description").val();
    $.ajax({
        type: 'POST',
        url: '{{ route('
        admin - edit_lecture_link ') }}',
        data: {
            '_token': $('input[name=_token]').val(),
            'id': $(this).data('id'),
            'class': $("#class").val(),
            'course': $("#course").val(),
            'coursetype': $("#coursetype").val(),
            'group': $("input[name='radio']:checked").val()

        },
        success: function(data) {
            $("#socket").hide(500);
            location.reload();
        },
    });
});

//--------------------------------------------------------------delete section--------------------------------------------------------------------------

$("body").delegate(".delete_subject", "click", function() {
    var id = $(this).data('id');
    var name = $(this).data('name');
    $(".spinner").hide();
    $('.socket_title').text('Delete Subject');
    $('.socket_body').empty().html('@csrf <a>Are You Sure! Want to delete subject</a><i> ( ' + name + ' ) </i><br><a style="color:#fff;" class="btn btn-success mt-1 delete_subject_confirm" style="margin:10px;" data-id="' + id + '" >Delete Subject</a>');
    $("#socket").show("closed");
});

$("body").delegate(".delete_subject_confirm", "click", function() {
    var id = $(this).data('id');
    $.ajax({
        type: 'POST',
        url: '{{ route('
        admin - delete_lecture_folder ') }}',
        data: {
            '_token': $('input[name=_token]').val(),
            'id': id,
        },
        success: function(data) {
            $("#socket").hide(500);
            location.reload();
        },
    });
});

$("body").delegate(".delete_topic", "click", function() {
    var id = $(this).data('id');
    var name = $(this).data('name');
    $(".spinner").hide();
    $('.socket_title').text('Delete Topic');
    $('.socket_body').empty().html('@csrf <a>Are You Sure! Want to delete Topic</a><i> ( ' + name + ' ) </i><br><a style="color:#fff;" class="btn btn-success mt-1 delete_topic_confirm" style="margin:10px;" data-id="' + id + '" >Delete Topic</a>');
    $("#socket").show("closed");
});

$("body").delegate(".delete_topic_confirm", "click", function() {
    var id = $(this).data('id');
    $.ajax({
        type: 'POST',
        url: '{{ route('
        admin - delete_lecture_subfolder ') }}',
        data: {
            '_token': $('input[name=_token]').val(),
            'id': id,
        },
        success: function(data) {
            $("#socket").hide(500);
            location.reload();
        },
    });
});
$("body").delegate(".delete_lecture", "click", function() {
    var id = $(this).data('id');
    var title = $(this).data('title');
    $(".spinner").hide();
    $('.socket_title').text('Delete Lecture');
    $('.socket_body').empty().html('@csrf <a>Are You Sure! Want to delete lecture</a><i> ( ' + title + ' ) </i><br><a style="color:#fff;" class="btn btn-success mt-1 delete_lecture_confirm" style="margin:10px;" data-id="' + id + '" >Delete Lecture</a>');
    $("#socket").show("closed");
});

$("body").delegate(".delete_lecture_confirm", "click", function() {
    var id = $(this).data('id');
    $.ajax({
        type: 'POST',
        url: '{{ route('
        admin - delete_lecture ') }}',
        data: {
            '_token': $('input[name=_token]').val(),
            'id': id,
        },
        success: function(data) {
            $("#socket").hide(500);
            location.reload();
        },
    });
});

$("body").delegate(".delete_lecture_link", "click", function() {
    var id = $(this).data('id');
    var name = $(this).data('name');
    $(".spinner").hide();
    $('.socket_title').text('Delete Lecture_link');
    $('.socket_body').empty().html('@csrf <a>Are You Sure! Want to delete published lecture link ?</a><br><a style="color:#fff;" class="btn btn-success mt-1 delete_lecture_link_confirm" style="margin:10px;" data-id="' + id + '" >Delete Published Lecture</a>');
    $("#socket").show("closed");
});

$("body").delegate(".delete_lecture_link_confirm", "click", function() {
    var id = $(this).data('id');
    $.ajax({
        type: 'POST',
        url: '{{ route('
        admin - delete_lecture_link ') }}',
        data: {
            '_token': $('input[name=_token]').val(),
            'id': id,
        },
        success: function(data) {
            $("#socket").hide(500);
            location.reload();
        },
    });
});

function fullscreen(id) {
    var loading = document.getElementById('loading');
    loading.style.display = '';
    var img = "{{ route('admin-mobile_video_lecture',['id'=>':year']) }}";
    var img = img.replace('%3Ayear', id);
    $.get(img, function(data) {
        $('#limiter').html("");
        $('#limiter').html(data);
        if (window.AndroidFunction) {
            AndroidFunction.landscape();
            $('#videoContainer').css('height', '100vh');
            $('#video').css('height', '100vh');
            $('.expand').attr('src', '{{ asset('
                img / shrink.png ') }}');
            $('#limiter').css('display', 'inline-flex');
            $('#wrapper').css('display', 'none');
        } else {
            var isInFullScreen = (document.fullscreenElement && document.fullscreenElement !== null) ||
                (document.webkitFullscreenElement && document.webkitFullscreenElement !== null) ||
                (document.mozFullScreenElement && document.mozFullScreenElement !== null) ||
                (document.msFullscreenElement && document.msFullscreenElement !== null);
            var docElm = document.getElementById("limiter");
            if (!isInFullScreen) {
                if (docElm.requestFullscreen) {
                    docElm.requestFullscreen();
                } else if (docElm.mozRequestFullScreen) {
                    docElm.mozRequestFullScreen();
                } else if (docElm.webkitRequestFullScreen) {
                    docElm.webkitRequestFullScreen();
                } else if (docElm.msRequestFullscreen) {
                    docElm.msRequestFullscreen();
                }
                screen.orientation.lock('landscape');
                $('#videoContainer').css('height', '100vh');
                $('#video').css('height', '100vh');
                $('.expand').attr('src', '{{ asset('
                    img / shrink.png ') }}');
                $('#limiter').css('display', 'inline-flex');
                $('#wrapper').css('display', 'none');


            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
                $('#videoContainer').css('height', '');
                $('#video').css('height', '');
                $('.expand').attr('src', '{{ asset('
                    img / expand.png ') }}');
                $('#limiter').css('display', 'none');
                $('#wrapper').css('display', '');
            }
        }
    });

}

</script>
@endsection
