<?php
namespace App\Http\Controllers;
use Validator;
use Response;
use File;
use Storage;
use disk;
use Auth;
use PDF;
use Zip;
use Session;
use newImage;
use ZanySoft\Zip\ZipManager;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\http\Requests;
use Illuminate\Http\Request;
use App\paper_link;
use App\student;
use App\result;
use App\teacher;
use App\time_left;
use App\admin;
use App\dpp;
use App\enquiry;
use App\dpp_link;
use App\advance_paper;
use App\answer;
use App\new_answer;
use App\normal_paper;
use App\online;
use App\question;
use App\new_question;
use App\chatbox;
use App\ts_folder;
use App\ts_folder_link;
use App\task_board;
use App\lecture;
use App\lecture_folder;
use App\lecture_link;
use App\lecture_subfolder;
use App\message;
use App\message_template;
use App\notification;
use App\notification_template;
use App\image;
use App\token;
use DB;
use Carbon\Carbon;

class AdminVideoLectureController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function video_lecture(Request $request)
    {
        $users = lecture::where(['id' => $request->get('id') , 'active' => '1'])
            ->get();
        return view('admin.video_lecture', compact('users'));
    }
    public function mobile_video_lecture(Request $request)
    {
        $users = lecture::where(['id' => $request->get('id') , 'active' => '1'])
            ->get();
        return view('admin.video_lecture_mobile', compact('users'));
    }
    public function lectures(Request $request)
    {
        if (!$request->has('fid') && !$request->has('sfid'))
        {
            $type = 'subject';
            $users = lecture_folder::where('active', '1')->orderBy('id', 'asc')
                ->get();
            return view('admin.lectures', compact('users', 'type'));
        }
        if ($request->has('fid') && !$request->has('sfid'))
        {
            $type = 'topic';
            $users = lecture_subfolder::where(['folder_id' => $request->fid, 'active' => '1'])
                ->orderBy('id', 'asc')
                ->get();
            return view('admin.lectures', compact('users', 'type'));
        }
        if ($request->has('fid') && $request->has('sfid'))
        {
            $type = 'lecture';
            $users = lecture::where(['folder_id' => $request->fid, 'subfolder_id' => $request->sfid, 'active' => '1'])
                ->orderBy('id', 'asc')
                ->get();
            return view('admin.lectures', compact('users', 'type'));
        }
    }
    public function lecture_links(Request $request)
    {
        $users = lecture_link::where(['lecture_id' => $request->get('id') , 'active' => '1'])
            ->orderBy('id', 'desc')
            ->get();
        return response()
            ->json($users);
    }
    public function add_lecture_folder(Request $request)
    {
        $file = new lecture_folder();
        $file->acd_id = Auth::user()->acd_id;
        $file->acd_name = Auth::user()->acd_name;
        $file->name = $request->name;
        $file->count = 0;
        $file->t_id = Auth::user()->id;
        $file->t_name = Auth::user()->name;
        $file->active = "1";
        $file->save();
        return Response::json($file);
    }

    public function edit_lecture_folder(Request $request)
    {
        $file = lecture_folder::where('id', $request->id)
            ->update(['name' => $request->name]);
        $file = lecture_subfolder::where('folder_id', $request->id)
            ->update(['folder_name' => $request->name]);
        $file = lecture_link::where('folder_id', $request->id)
            ->update(['folder_name' => $request->name]);
        $file = lecture::where('folder_id', $request->id)
            ->update(['folder_name' => $request->name]);
        return Response::json($file);
    }

    public function delete_lecture_folder(Request $request)
    {
        $file = lecture_folder::where('id', $request->id)
            ->update(['active' => 0]);
        $file = lecture_subfolder::where('folder_id', $request->id)
            ->update(['active' => 0]);
        $file = lecture_link::where('folder_id', $request->id)
            ->update(['active' => 0]);
        $file = lecture::where('folder_id', $request->id)
            ->update(['active' => 0]);
        return Response::json($file);
    }

    public function add_lecture_subfolder(Request $request)
    {
        $file = new lecture_subfolder();
        $file->acd_id = Auth::user()->acd_id;
        $file->acd_name = Auth::user()->acd_name;
        $file->folder_id = $request->folder_id;
        $file->folder_name = $request->folder_name;
        $file->name = $request->name;
        $file->count = 0;
        $file->t_id = Auth::user()->id;
        $file->t_name = Auth::user()->name;
        $file->active = "1";
        $file->save();
        $files = lecture_folder::find($request->folder_id);
        $files->count = $files->count + 1;
        $files->save();

        return Response::json($file);
    }

    public function edit_lecture_subfolder(Request $request)
    {
        $file = lecture_subfolder::where('id', $request->id)
            ->update(['name' => $request->name]);
        $file = lecture_link::where('subfolder_id', $request->id)
            ->update(['subfolder_name' => $request->name]);
        $file = lecture::where('subfolder_id', $request->id)
            ->update(['subfolder_name' => $request->name]);
        return Response::json($file);
    }

    public function delete_lecture_subfolder(Request $request)
    {
        $filess = lecture_subfolder::find($request->id);
        $files = lecture_folder::find($filess->folder_id);
        $files->count = $files->count - 1;
        $files->save();
        $file = lecture_subfolder::where('id', $request->id)
            ->update(['active' => 0]);
        $file = lecture_link::where('subfolder_id', $request->id)
            ->update(['active' => 0]);
        $file = lecture::where('subfolder_id', $request->id)
            ->update(['active' => 0]);
        return Response::json($file);
    }

    public function add_lecture(Request $request)
    {
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $request->video_code, $match);

        if (isset($match[1]))
        {
            $youtube_id = $match[1];
        }
        else
        {
            $youtube_id = $request->video_code;
        }
        $file = new lecture();
        $file->acd_id = Auth::user()->acd_id;
        $file->acd_name = Auth::user()->acd_name;
        $file->folder_id = $request->folder_id;
        $file->folder_name = $request->folder_name;
        $file->subfolder_id = $request->subfolder_id;
        $file->subfolder_name = $request->subfolder_name;
        $file->title = $request->title;
        $file->video_code = $youtube_id;
        $file->description = $request->description;
        $file->count = 0;
        $file->t_id = Auth::user()->id;
        $file->t_name = Auth::user()->name;
        $file->active = "1";
        $file->save();
        $files = lecture_subfolder::find($request->subfolder_id);
        $files->count = $files->count + 1;
        $files->save();

        return Response::json($file);
    }

    public function edit_lecture(Request $request)
    {
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $request->video_code, $match);
        if (isset($match[1]))
        {
            $youtube_id = $match[1];
        }
        else
        {
            $youtube_id = $request->video_code;
        }
        $file = lecture::where('id', $request->id)
            ->update(['title' => $request->title, 'video_code' => $youtube_id, 'description' => $request->description]);
        $file = lecture_link::where('lecture_id', $request->id)
            ->update(['title' => $request->title, 'video_code' => $request->video_code, 'description' => $request->description]);
        return Response::json($file);
    }

    public function delete_lecture(Request $request)
    {
        $filess = lecture::find($request->id);
        $files = lecture_subfolder::find($filess->subfolder_id);
        $files->count = $files->count - 1;
        $files->save();
        $file = lecture::where('id', $request->id)
            ->update(['active' => 0]);
        $file = lecture_link::where('lecture_id', $request->id)
            ->update(['active' => 0]);
        return Response::json($file);
    }

    public function add_lecture_link(Request $request)
    {
        $files = lecture::find($request->lecture_id);
        $file = new lecture_link();
        $file->acd_id = Auth::user()->acd_id;
        $file->acd_name = Auth::user()->acd_name;
        $file->folder_id = $files->folder_id;
        $file->folder_name = $files->folder_name;
        $file->subfolder_id = $files->subfolder_id;
        $file->subfolder_name = $files->subfolder_name;
        $file->lecture_id = $files->id;
        $file->title = $files->title;
        $file->video_code = $files->video_code;
        $file->description = $files->description;
        $file->classid = $request->class;
        $file->courseid = $request->course;
        $file->coursetypeid = $request->coursetype;
        $file->groupid = $request->group;
        $file->cccgid = $request->class . $request->course . $request->coursetype . $request->group;
        $file->t_id = Auth::user()->id;
        $file->t_name = Auth::user()->name;
        $file->active = "1";
        $file->save();
        $subject = $files->folder_name;
        $topic = $files->subfolder_name;
        $lecture = $files->title;
        $files->count = $files->count + 1;
        $files->save();
        //---------------------------------------------------------------------Notification section--------------------------------------------
        $title = "New Lecture Published";
        $body = "A new (" . $subject . ") video lecture and notes of " . $lecture . " for " . $topic . " has been published.";
        $summary = "Lecture Publish";

        $acd_id = Auth::user()->acd_id;
        $acd_name = Auth::user()->acd_name;
        $notification_type = 'right_icon_long';
        $title = $title;
        $body = $body;
        $title_long = $title;
        $body_long = $body;
        $title_line = null;
        $body_line1 = null;
        $body_line2 = null;
        $body_line3 = null;
        $body_line4 = null;
        $body_line5 = null;
        $body_line6 = null;
        $body_line7 = null;
        $body_line8 = null;
        $body_line9 = null;
        $body_line10 = null;
        $summary = $summary;
        $icon = asset('') . env('NOTI_ICON');
        $image = asset('') . env('NOTI_ICON');

        $browser_token = array();
        $br_tk = 0;
        $app_tk = 0;
        $app_token = array();
        $url = 'https://fcm.googleapis.com/fcm/send';

        $userss = student::where(['class' => $request->class, 'course' => $request->course, 'coursetype' => $request->coursetype, 'groupid' => $request->group, 'active' => '1'])
            ->select('id')
            ->get();
        foreach ($userss as $users)
        {
            $use = token::where(['user_id' => $users->id, 'user_type' => 'student', 'active' => '1'])
                ->get();
            foreach ($use as $user)
            {
                if ($user->token_type == "Application")
                {
                    $app_token[$app_tk] = $user->token;
                    $app_tk++;
                }
                else
                {
                    $browser_token[$br_tk] = $user->token;
                    $br_tk++;
                }
            }
        }

        $headers = array(
            "Authorization: key=" . env('FCM_SERVER_KEY') ,
            'Content-Type: application/json'
        );
        $app_tokens = array_chunk($app_token, 999, true);
        foreach ($app_tokens as $token)
        {
            $app_fields = array(
                'registration_ids' => $token,
                'data' => array(
                    "title" => $title,
                    "body" => $body,
                    "title_long" => $title_long,
                    "body_long" => $body_long,
                    "title_line" => $title_line,
                    "body_line1" => $body_line1,
                    "body_line2" => $body_line2,
                    "body_line3" => $body_line3,
                    "body_line4" => $body_line4,
                    "body_line5" => $body_line5,
                    "body_line6" => $body_line6,
                    "body_line7" => $body_line7,
                    "body_line8" => $body_line8,
                    "body_line9" => $body_line9,
                    "body_line10" => $body_line10,
                    "summary" => $summary,
                    "icon" => $icon,
                    "sound" => "notification",
                    "noti_id" => rand(1, 1000) ,
                    "channel_id" => "Paper Publish",
                    "image" => $image,
                    "type" => $notification_type,
                    "click_action" => "https://deltatrek.in/user/login"
                )
            );

            $fields = json_encode($app_fields);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            $result = curl_exec($ch);
            curl_close($ch);
        }
        $browser_tokens = array_chunk($browser_token, 999, true);
        foreach ($browser_tokens as $token)
        {
            if ($notification_type == 'no_icon')
            {
                $title = $title;
                $body = $body;
                $image = '';
                $icon = 'https://deltatrek.in/img/mobile%20ins.png';
            }
            elseif ($notification_type == 'right_icon' || $notification_type == 'left_icon')
            {
                $title = $title;
                $body = $body;
                $image = '';
                $icon = $icon;
            }
            elseif ($notification_type == 'right_icon_long')
            {
                $title = $title_long;
                $body = $body_long;
                $image = '';
                $icon = $icon;
            }
            elseif ($notification_type == 'no_icon_long')
            {
                $title = $title_long;
                $body = $body_long;
                $image = '';
                $icon = 'https://deltatrek.in/img/mobile%20ins.png';
            }
            elseif ($notification_type == 'no_icon_image')
            {
                $title = $title;
                $body = $body;
                $image = $image;
                $icon = 'https://deltatrek.in/img/mobile%20ins.png';
            }
            elseif ($notification_type == 'right_icon_image_hide' || $notification_type == 'right_icon_image_show')
            {
                $title = $title;
                $body = $body;
                $image = $image;
                $icon = $icon;
            }
            elseif ($notification_type == 'no_icon_lines')
            {
                $title = $title_line;
                $body = $body_line1 . ' ' . $body_line2 . ' ' . $body_line3 . ' ' . $body_line4 . ' ' . $body_line5 . ' ' . $body_line6 . ' ' . $body_line7 . ' ' . $body_line8 . ' ' . $body_line9 . ' ' . $body_line10;
                $image = '';
                $icon = 'https://deltatrek.in/img/mobile%20ins.png';
            }
            elseif ($notification_type == 'right_icon_lines')
            {
                $title = $title_line;
                $body = $body_line1 . ' ' . $body_line2 . ' ' . $body_line3 . ' ' . $body_line4 . ' ' . $body_line5 . ' ' . $body_line6 . ' ' . $body_line7 . ' ' . $body_line8 . ' ' . $body_line9 . ' ' . $body_line10;
                $image = '';
                $icon = $icon;
            }
            $browser_fields = array(
                'registration_ids' => $token,
                'notification' => array(
                    "title" => $title,
                    "body" => $body,
                    "icon" => $icon,
                    "sound" => "notification",
                    "noti_id" => rand(1, 1000) ,
                    "image" => $image,
                    "click_action" => "https://deltatrek.in/user/login"
                )
            );

            $fields = json_encode($browser_fields);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            $result = curl_exec($ch);
            curl_close($ch);
        }
        return Response::json($file);
    }

    public function edit_lecture_link(Request $request)
    {
        $file = lecture_link::where('id', $request->id)
            ->update(['classid' => $request->class, 'courseid' => $request->course, 'coursetypeid' => $request->coursetype, 'groupid' => $request->group, 'cccgid' => $request->class . $request->course . $request->coursetype . $request->group]);
        //---------------------------------------------------------------------Notification section--------------------------------------------
        $files = lecture_link::find($request->id);
        $subject = $files->folder_name;
        $topic = $files->subfolder_name;
        $lecture = $files->title;

        $title = "New Lecture Published";
        $body = "A new (" . $subject . ") video lecture and notes of " . $lecture . " for " . $topic . " has been published.";
        $summary = "Lecture Publish";

        $acd_id = Auth::user()->acd_id;
        $acd_name = Auth::user()->acd_name;
        $notification_type = 'right_icon_long';
        $title = $title;
        $body = $body;
        $title_long = $title;
        $body_long = $body;
        $title_line = null;
        $body_line1 = null;
        $body_line2 = null;
        $body_line3 = null;
        $body_line4 = null;
        $body_line5 = null;
        $body_line6 = null;
        $body_line7 = null;
        $body_line8 = null;
        $body_line9 = null;
        $body_line10 = null;
        $summary = $summary;
        $icon = asset('') . env('NOTI_ICON');
        $image = asset('') . env('NOTI_ICON');
        $class = $request->class;
        $course = $request->course;
        $coursetype = $request->coursetype;
        $group = $request->group;
        $cccgid = $request->class . $request->course . $request->coursetype . $request->group;

        $browser_token = array();
        $br_tk = 0;
        $app_tk = 0;
        $app_token = array();
        $url = 'https://fcm.googleapis.com/fcm/send';

        $userss = student::where(['class' => $request->class, 'course' => $request->course, 'coursetype' => $request->coursetype, 'groupid' => $request->group, 'active' => '1'])
            ->select('id')
            ->get();
        foreach ($userss as $users)
        {
            $use = token::where(['user_id' => $users->id, 'user_type' => 'student', 'active' => '1'])
                ->get();
            foreach ($use as $user)
            {
                if ($user->token_type == "Application")
                {
                    $app_token[$app_tk] = $user->token;
                    $app_tk++;
                }
                else
                {
                    $browser_token[$br_tk] = $user->token;
                    $br_tk++;
                }
            }
        }

        $headers = array(
            "Authorization: key=" . env('FCM_SERVER_KEY') ,
            'Content-Type: application/json'
        );
        $app_tokens = array_chunk($app_token, 999, true);
        foreach ($app_tokens as $token)
        {
            $app_fields = array(
                'registration_ids' => $token,
                'data' => array(
                    "title" => $title,
                    "body" => $body,
                    "title_long" => $title_long,
                    "body_long" => $body_long,
                    "title_line" => $title_line,
                    "body_line1" => $body_line1,
                    "body_line2" => $body_line2,
                    "body_line3" => $body_line3,
                    "body_line4" => $body_line4,
                    "body_line5" => $body_line5,
                    "body_line6" => $body_line6,
                    "body_line7" => $body_line7,
                    "body_line8" => $body_line8,
                    "body_line9" => $body_line9,
                    "body_line10" => $body_line10,
                    "summary" => $summary,
                    "icon" => $icon,
                    "sound" => "notification",
                    "noti_id" => rand(1, 1000) ,
                    "channel_id" => "Paper Publish",
                    "image" => $image,
                    "type" => $notification_type,
                    "click_action" => "https://deltatrek.in/user/login"
                )
            );

            $fields = json_encode($app_fields);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            $result = curl_exec($ch);
            curl_close($ch);
        }
        $browser_tokens = array_chunk($browser_token, 999, true);
        foreach ($browser_tokens as $token)
        {
            if ($notification_type == 'no_icon')
            {
                $title = $title;
                $body = $body;
                $image = '';
                $icon = 'https://deltatrek.in/img/mobile%20ins.png';
            }
            elseif ($notification_type == 'right_icon' || $notification_type == 'left_icon')
            {
                $title = $title;
                $body = $body;
                $image = '';
                $icon = $icon;
            }
            elseif ($notification_type == 'right_icon_long')
            {
                $title = $title_long;
                $body = $body_long;
                $image = '';
                $icon = $icon;
            }
            elseif ($notification_type == 'no_icon_long')
            {
                $title = $title_long;
                $body = $body_long;
                $image = '';
                $icon = 'https://deltatrek.in/img/mobile%20ins.png';
            }
            elseif ($notification_type == 'no_icon_image')
            {
                $title = $title;
                $body = $body;
                $image = $image;
                $icon = 'https://deltatrek.in/img/mobile%20ins.png';
            }
            elseif ($notification_type == 'right_icon_image_hide' || $notification_type == 'right_icon_image_show')
            {
                $title = $title;
                $body = $body;
                $image = $image;
                $icon = $icon;
            }
            elseif ($notification_type == 'no_icon_lines')
            {
                $title = $title_line;
                $body = $body_line1 . ' ' . $body_line2 . ' ' . $body_line3 . ' ' . $body_line4 . ' ' . $body_line5 . ' ' . $body_line6 . ' ' . $body_line7 . ' ' . $body_line8 . ' ' . $body_line9 . ' ' . $body_line10;
                $image = '';
                $icon = 'https://deltatrek.in/img/mobile%20ins.png';
            }
            elseif ($notification_type == 'right_icon_lines')
            {
                $title = $title_line;
                $body = $body_line1 . ' ' . $body_line2 . ' ' . $body_line3 . ' ' . $body_line4 . ' ' . $body_line5 . ' ' . $body_line6 . ' ' . $body_line7 . ' ' . $body_line8 . ' ' . $body_line9 . ' ' . $body_line10;
                $image = '';
                $icon = $icon;
            }
            $browser_fields = array(
                'registration_ids' => $token,
                'notification' => array(
                    "title" => $title,
                    "body" => $body,
                    "icon" => $icon,
                    "sound" => "notification",
                    "noti_id" => rand(1, 1000) ,
                    "image" => $image,
                    "click_action" => "https://deltatrek.in/user/login"
                )
            );

            $fields = json_encode($browser_fields);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            $result = curl_exec($ch);
            curl_close($ch);
        }
        return Response::json($file);
    }

    public function delete_lecture_link(Request $request)
    {
        $filess = lecture_link::find($request->id);
        $files = lecture::find($filess->lecture_id);
        $files->count = $files->count - 1;
        $files->save();
        $filess->active = 0;
        $filess->save();
        return Response::json($filess);
    }

}

