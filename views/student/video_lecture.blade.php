@extends('layout/student_dashboard')
@extends('layout/details')
@section('popup')
@endsection
@section('inner_block')
<link rel="stylesheet" type="text/css" href="{{ asset('css/videocss.css') }}">
@foreach($users as $user)
<div id="limiter" class="limiter col-md-12" style="font-size: 13px; padding-right: 0.4rem;padding-left: 0.4rem;">
    <div id="noty_full" style="z-index: 9999999999; position: fixed;right:10px;bottom: 10px; border-radius: 10px;width: 300px; "></div>
    <div id="videoContainer" class="col-md-12 px-0">
        <div class="embed-responsive embed-responsive-4by3" id="video">
            <div class="overlay">
                <div id="headbar" style="position: absolute;width: 100%;height: 60px; background-color: #00e0fd; z-index: 9999;">
                    <ul class="online_student font-weight-bold" style="background: transparent;width: auto; font-size: 20px; color: #30180fc4;">
                        <li><b>Lecture : {{$user->title}}</b></li>
                    </ul>
                </div>
            </div>
            <style type="text/css">
            .online_student {
                display: inline-flex;
                width: auto;
                padding: 10px;
                margin: 5px;
                border: 1px #4d4d4d;
                z-index: 99;
                max-height: 80vh;
                overflow-y: hide;
                list-style: none;
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
            <div id="controlss" onmouseleave="controlblur()" onmouseover="controlfocus()">
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
                    <div class="scale" onclick="fullscreen()">
                        <div class="icon"><img class="icon expand" src="{{ asset('img/expand.png') }}"></div>
                    </div>
                </div>
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
@section('js')
<script type="text/javascript">
</script>
<script src="https://www.youtube.com/iframe_api"></script>
<script type="text/javascript">
var player,
    time_update_interval = 0;

function onYouTubeIframeAPIReady() {
    player = new YT.Player('video-placeholder', {
        videoId: '{{$user->video_code}}',
        playerVars: {
            color: 'transparent',
            autohide: 0,
            version: 3,
            cc_load_policy: 0,
            controls: 0,
            disablekb: 0,
            iv_load_policy: 0,
            modestbranding: 0,
            rel: 0,
            showinfo: 0,
            start: 0
        },
        events: {

            onReady: initialize,
            onPlaybackQualityChange: change,

        }
    });
}

function initialize(event) {
    //var embedCode = event.target.getVideoEmbedCode();
    //event.target.playVideo();
    //document.getElementById('video').innerHTML = embedCode+'<style type="text/css">.ytp-expand-pause-overlay .ytp-pause-overlay {display: none;</style>';
    //console.log(embedCode);

    // Update the controls on load
    updateTimerDisplay();
    updateProgressBar();

    // Clear any old interval.
    clearInterval(time_update_interval);

    // Start interval to update elapsed time display and
    // the elapsed part of the progress bar every second.
    time_update_interval = setInterval(function() {
        updateTimerDisplay();
        updateProgressBar();
    }, 1000);


    $('intensity').css('width', player.getVolume());
    $('#speed').val(player.getPlaybackRate());

}

function change(event) {

    $('#speed').val(player.getPlaybackRate());
    console.log(player.getPlaybackRate());
}


// This function is called by initialize()
function updateTimerDisplay() {
    // Update current time text display.
    $('#current-time').text(formatTime(player.getCurrentTime()) + ' / ' + formatTime(player.getDuration()));
}


// This function is called by initialize()
function updateProgressBar() {
    var fraction = (player.hasOwnProperty('getVideoLoadedFraction') ?
        player.getVideoLoadedFraction() :
        0);
    // Update the value of our progress bar accordingly.
    $('.progress').css('width', ((player.getCurrentTime() / player.getDuration()) * 100) + '%');
    $('.progress1').css('width', ((fraction * 100).toFixed(1)) + '%');


}

function updateProgressBar1(time) {
    var fraction = (player.hasOwnProperty('getVideoLoadedFraction') ?
        player.getVideoLoadedFraction() :
        0);
    // Update the value of our progress bar accordingly.
    $('.progress').css('width', ((time / player.getDuration()) * 100) + '%');
    $('.progress1').css('width', ((fraction * 100).toFixed(1)) + '%');


}


// Progress bar
function changeprogress(event) {


    var mouseX = event.pageX - $('.progressBar').offset().left,
        width = $('.progressBar').outerWidth();
    var time = player.getDuration() * (mouseX / width)
    player.seekTo(time);
    //console.log((mouseX / width)*100);
    updateProgressBar1(mouseX / width);
}

function controlfocus() {
    $('#controls').show();
    $('#headbar').show();

}

function controlblur() {
    setTimeout(function() {
        if (player.getPlayerState() == 1) {
            $('#controls').hide();
            $('#headbar').hide();
        }
    }, 700);

}
$(window).keypress(function(e) {
    if (e.keyCode == 0 || e.keyCode == 32) {
        if (player.getPlayerState() == 1) {
            player.pauseVideo();
            controlfocus();
            $('.playPause').css('display', 'none');
            $('.playButton').css('display', '');
        } else if (player.getPlayerState() == 2 || player.getPlayerState() == 5) {
            player.playVideo();
            if (player.getPlayerState() == 5) {
                setTimeout(function() { controlblur(); }, 4000);
            } else {
                controlblur();
            }
            var time = player.getCurrentTime();
            $('.playButton').css('display', 'none');
            $('.playPause').css('display', '');
        }
    }
});
$('.overlay').on('click', function() {
    if (player.getPlayerState() == 1) {
        player.pauseVideo();
        controlfocus();
        $('.playPause').css('display', 'none');
        $('.playButton').css('display', '');
    } else if (player.getPlayerState() == 2 || player.getPlayerState() == 5) {
        player.playVideo();
        if (player.getPlayerState() == 5) {
            setTimeout(function() { controlblur(); }, 4000);
        } else {
            controlblur();
        }
        var time = player.getCurrentTime();
        $('.playButton').css('display', 'none');
        $('.playPause').css('display', '');
    }
});

$('#play').on('click', function() {
    player.playVideo();
    if (player.getPlayerState() == 5) {
        setTimeout(function() { controlblur(); }, 4000);
    } else {
        controlblur();
    }
    var time = player.getCurrentTime();
    $('.playButton').css('display', 'none');
    $('.playPause').css('display', '');
});

$('#pause').on('click', function() {
    player.pauseVideo();
    controlfocus();
    $('.playPause').css('display', 'none');
    $('.playButton').css('display', '');
});


$('#mute-toggle').on('click', function() {

    if (player.isMuted()) {
        player.unMute();
        $('.muteyes').css('display', 'none');
        $('.muteno').css('display', '');
    } else {
        player.mute();
        $('.muteno').css('display', 'none');
        $('.muteyes').css('display', '');
    }
});

function changevol() {
    var mouseX = event.pageX - $('.intensityBar').offset().left,
        width = $('.intensityBar').outerWidth();
    player.setVolume(mouseX);
    $('.intensity').css('width', mouseX + '%');
}
// Other options


$('#speed').on('change', function() {
    var rate = $(this).val();
    $('#ytp-menu-speed').parent().find('.ytp-button:contains("' + rate + '")').click();
});

$('#quality').on('change', function() {
    var rate = $(this).val();
    $('#ytp-menu-speed').parent().find('.ytp-button:contains(' + rate + ')').click();
});


// Playlist

$('#next').on('click', function() {
    player.nextVideo()
});

$('#prev').on('click', function() {
    player.previousVideo()
});

// Helper Functions

function formatTime(time) {
    time = Math.round(time);

    var minutes = Math.floor(time / 60),
        seconds = time - minutes * 60;

    seconds = seconds < 10 ? '0' + seconds : seconds;

    return minutes + ":" + seconds;
}

function getFullScreenElement() {
    return document.FullscreenElement ||
        document.webkitFullscreenElement ||
        document.mozFullscreenElement ||
        document.msFullscreenElement;

}

function fullscreen1() {
    // Go into full screen first
    if (document.documentElement.requestFullscreen) {
        document.documentElement.requestFullscreen();
    } else if (document.documentElement.mozRequestFullScreen) {
        document.documentElement.mozRequestFullScreen();
    } else if (document.documentElement.webkitRequestFullscreen) {
        document.documentElement.webkitRequestFullscreen();
    } else if (document.documentElement.msRequestFullscreen) {
        document.documentElement.msRequestFullscreen();
    }

    // Then lock orientation
    fullscreen1();
}

function fullscreen() {
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
        $('#limiter').css('padding-left', '0rem');
        $('#limiter').css('padding-right', '0rem');
        $('#videoContainer').css('height', '100vh');
        $('#video').css('height', '100vh');
        $('.expand').attr('src', '{{ asset('
            img / shrink.png ') }}');


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
        $('#limiter').css('padding-left', '0.4rem');
        $('#limiter').css('padding-right', '0.4rem');
        $('.expand').attr('src', '{{ asset('
            img / expand.png ') }}');
    }
}

</script>
<script>
$(window).on("scroll", function() {
    var scroll = $(window).scrollTop();

    if (scroll >= 80) {
        $("#site-header").addClass("nav-fixed");
    } else {
        $("#site-header").removeClass("nav-fixed");
    }
});

//Main navigation Active Class Add Remove
$(".navbar-toggler").on("click", function() {
    $("header").toggleClass("active");
});
$(document).on("ready", function() {
    if ($(window).width() > 991) {
        $("header").removeClass("active");
    }
    $(window).on("resize", function() {
        if ($(window).width() > 991) {
            $("header").removeClass("active");
        }
    });
});
$(window).on("load", function() {
    if ($(window).width() < 1010) {
        lock();
    }
});



if (document.addEventListener) {
    document.addEventListener('fullscreenchange', exitHandler, false);
    document.addEventListener('mozfullscreenchange', exitHandler, false);
    document.addEventListener('MSFullscreenChange', exitHandler, false);
    document.addEventListener('webkitfullscreenchange', exitHandler, false);
}

function exitHandler() {
    if (!document.webkitIsFullScreen && !document.mozFullScreen && !document.msFullscreenElement) {
        $('#videoContainer').css('height', '');
        $('#video').css('height', '');
        $('#videoContainer').css('width', '');
        $('#limiter').css('padding-left', '0.4rem');
        $('#limiter').css('padding-right', '0.4rem');
        $('#video').css('width', '');
        $('.expand').attr('src', '{{ asset('
            img / expand.png ') }}');
    }
}

</script>
@endforeach
@endsection
