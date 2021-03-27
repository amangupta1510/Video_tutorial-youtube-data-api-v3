<?php
namespace App\Http\Controllers;
use Validator;
use Response;
use File;
use Auth;
use Storage;
use PDF;
use Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\http\Requests;
use Illuminate\Http\Request;
use App\result;
use App\time_left;
use App\paper_link;
use App\dpp;
use App\dpp_link;
use App\advance_paper;
use App\normal_paper;
use App\custom_paper;
use App\question;
use App\new_question;
use App\answer;
use App\new_answer;
use App\student;
use App\chatbox;
use App\teacher;
use App\classroom;
use App\class_chat;
use App\class_user;
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
use App\token;
use newImage;
//use App\fcm_msg;
use DB;
use Carbon\Carbon;

class studentcontroller extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:student');
    }

    public function index(Request $request)
    {

        if (Session::get('token') != NULL)
        {
            $count = 0;
            $token = Session::get('token');
            $token_type = Session::get('token_type');
            $version_code = Session::get('version_code');
            $count = token::Where(['token' => $token, 'user_id' => Auth::user()->id, 'user_type' => 'student', 'active' => '1'])
                ->count();
            if ($count == 0)
            {
                $tk = new token();
                $tk->acd_id = Auth::user()->acd_id;
                $tk->acd_name = Auth::user()->acd_name;
                $tk->user_id = Auth::user()->id;
                $tk->user_name = Auth::user()->name;
                $tk->user_type = 'student';
                $tk->token = $token;
                $tk->token_type = $token_type;
                $tk->version_code = $version_code;
                $tk->active = "1";
                $tk->save();
            }
            Session::forget('token');
            Session::forget('token_type');
            Session::forget('version_code');

        }
        //'cccgid'=>Auth::user()->class.Auth::user()->course.Auth::user()->coursetype.Auth::user()->groupid,
        $users = task_board::where(['cccgid' => Auth::user()->class . Auth::user()->course . Auth::user()->coursetype . Auth::user()->groupid, 'active' => '1'])
            ->orderBy('publish_date', 'desc')
            ->paginate(30);
        return view('student.profile', compact('users'));
    }

    // function sendsFCM()
    // {
    //     //'to' => '/topics/oksir',
    //     $url = 'https://iid.googleapis.com/iid/v1/cPMqPv1oTYOkKQfX1V2VaP:APA91bGe_cZO3QJUC-6aIZQSQNn__1i501-sJdjv9DIj7C44afdtIStOj7bpMf2LKSuqv2G1u97oq-G0QNzaJWR7I8z90qbiEZDD2d8Tdez2xh2R5FiNAXpQfPQijeZ-s6cpEGnXCzNo/rel/topics/oksir';
    //     $me = array(
    //         'methode' => "DELETE"
    //     );
    //     $headers = array(
    //         'Content-Type: application/json',
    //         "Authorization: key=AAAArzPeXr4:APA91bFd8tJRlZITjr7YfTjAC3EdpPN0szPp4VeySKAkUTDUx_BuqmBHjLT2wXECWvCd4gTl0WqKMC62HRLGvvZxEB7s_E-ZyJ_b5TBe81Fgka0Gc4cYmICJIbm9Q7SrkYIEktFpJhTE"
    //     );
    //     $fields = json_encode($me);
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, $url);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    //     $result = curl_exec($ch);
    //     curl_close($ch);
    //     return Response::json($result);
    // }
    // function sendFCM()
    // {
    //     //'to' => '/topics/oksir',
    //     $count = 0;
    //     $users = student::where(['class' => '11th', 'course' => 'NEET + AIIMS', 'coursetype' => 'Classroom Course', 'groupid' => 'E', 'active' => '1'])->select('id')
    //         ->get();
    //     foreach ($users as $user)
    //     {
    //         $use = token::where(['user_id' => $user->id, 'user_type' => 'student', 'active' => '1'])
    //             ->get();
    //         foreach ($use as $usex)
    //         {
    //             $url = 'https://fcm.googleapis.com/fcm/send';
    //             if ($usex->token_type == "Application")
    //             {
    //                 $fields = array(
    //                     'to' => $usex->token,
    //                     'data' => array(
    //                         "title" => "Dipak Gupta Sir Assigned New Task",
    //                         "body" => "Physics Practice Sheet - 3",
    //                         "title_long" => "Physics Practice Sheet - 3",
    //                         "body_long" => "As discussed in today's lecture solve the question of graphs. From Q98 to Q115 and Q226 to Q248 (Total 40 Questions). We'll discuss the doubts on saturday.",
    //                         "title_line" => "line1",
    //                         "body_line1" => "line1",
    //                         "body_line2" => "line1",
    //                         "body_line3" => "line1",
    //                         "body_line4" => "line1",
    //                         "body_line5" => "line1",
    //                         "body_line6" => "line1",
    //                         "body_line7" => "line1",
    //                         "body_line8" => "line1",
    //                         "body_line9" => "line1",
    //                         "body_line10" => "line1",
    //                         "summary" => "New Task",
    //                         "icon" => "https://cdn.pixabay.com/photo/2015/04/23/22/00/tree-736885__340.jpg",
    //                         "sound" => "notification",
    //                         "noti_id" => rand(3, 8) ,
    //                         "channel_id" => "Task",
    //                         //"image"=>"https://cdn.pixabay.com/photo/2015/04/23/22/00/tree-736885__340.jpg",
    //                         "type" => "right_icon_long",
    //                         "click_action" => "https://deltatrek.in/user/login"
    //                     )
    //                 );
    //             }
    //             else
    //             {
    //                 $fields = array(
    //                     'to' => $usex->token,
    //                     'notification' => array(
    //                         "title" => "Dipak Gupta Sir Assigned New Task",
    //                         "body" => "Physics Practice Sheet - 3",
    //                         "title_long" => "Physics Practice Sheet - 3",
    //                         "body_long" => "As discussed in today's lecture solve the question of graphs. From Q98 to Q115 and Q226 to Q248 (Total 40 Questions). We'll discuss the doubts on saturday.",
    //                         "title_line" => "line1",
    //                         "body_line1" => "line1",
    //                         "body_line2" => "line1",
    //                         "body_line3" => "line1",
    //                         "body_line4" => "line1",
    //                         "body_line5" => "line1",
    //                         "body_line6" => "line1",
    //                         "body_line7" => "line1",
    //                         "body_line8" => "line1",
    //                         "body_line9" => "line1",
    //                         "body_line10" => "line1",
    //                         "summary" => "New Task",
    //                         //"icon" => "https://cdn.pixabay.com/photo/2015/04/23/22/00/tree-736885__340.jpg",
    //                         "sound" => "notification",
    //                         "noti_id" => rand(3, 8) ,
    //                         "channel_id" => "Task",
    //                         "image" => "https://cdn.pixabay.com/photo/2015/04/23/22/00/tree-736885__340.jpg",
    //                         "type" => "right_icon_long",
    //                         "click_action" => "https://deltatrek.in/user/login"
    //                     )
    //                 );
    //             }
    //             $count++;
    //             $fields = json_encode($fields);
    //             $headers = array(
    //                 "Authorization: key=AAAArzPeXr4:APA91bFd8tJRlZITjr7YfTjAC3EdpPN0szPp4VeySKAkUTDUx_BuqmBHjLT2wXECWvCd4gTl0WqKMC62HRLGvvZxEB7s_E-ZyJ_b5TBe81Fgka0Gc4cYmICJIbm9Q7SrkYIEktFpJhTE",
    //                 'Content-Type: application/json'
    //             );
    //             $ch = curl_init();
    //             curl_setopt($ch, CURLOPT_URL, $url);
    //             curl_setopt($ch, CURLOPT_POST, true);
    //             curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //             curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    //             $result = curl_exec($ch);
    //             curl_close($ch);
    //         }
    //     }
    //     return Response::json($count);
    // }
    public function task_progress_update(Request $request)
    {
        $dateinit = \Carbon\Carbon::parse($request->dateini);
        $datefim = \Carbon\Carbon::parse($request->datefim);
        $task = task_board::find($request->id);
        $vx = 0;
        $no = 0;
        if ($task->complete != NULL)
        {
            $data = json_decode($task->complete, true);
            for ($i = 0;$i < sizeof($data);$i++)
            { //return Response::json($val);
                if ($data[$i]['s_id'] == Auth::user()->id)
                {
                    $vx = 1;
                    $data[$i]['complete'] = $request->val;
                    $task->complete = json_encode($data);
                    break;
                }
                $no++;
            }
        }
        if ($vx == 0)
        {
            if ($task->complete != NULL)
            {
                $str = array(
                    "s_id" => Auth::user()->id,
                    "s_name" => Auth::user()->name,
                    "complete" => $request->val,
                    "mark" => 'reed',
                    "time" => $dateinit->format('d M Y H:i')
                );
                $arr = json_decode($task->complete, true);
                array_push($arr, $str);
                $str = json_encode($arr);
                $task->count = $task->count + 1;
            }
            else
            {
                $str = array(
                    array(
                        "s_id" => Auth::user()->id,
                        "s_name" => Auth::user()->name,
                        "complete" => $request->val,
                        "mark" => 'reed',
                        "time" => $dateinit->format('d M Y H:i')
                    )
                );
                $str = json_encode($str);
                $task->count = $task->count + 1;
            }
            $task->complete = $str;
        }

        $task->save();
        return Response::json(json_decode($task->complete));
    }

    public function task_status_update(Request $request)
    {
        $dateinit = \Carbon\Carbon::parse($request->dateini);
        $datefim = \Carbon\Carbon::parse($request->datefim);
        $task = task_board::find($request->id);
        $vx = 0;
        $no = 0;
        if ($task->complete != NULL)
        {
            $data = json_decode($task->complete, true);
            for ($i = 0;$i < sizeof($data);$i++)
            { //return Response::json($val);
                if ($data[$i]['s_id'] == Auth::user()->id)
                {
                    $vx = 1;
                    $data[$i]['mark'] = $request->mark;
                    $task->complete = json_encode($data);
                    break;
                }
                $no++;
            }
        }
        if ($vx == 0)
        {
            if ($task->complete != NULL)
            {
                $str = array(
                    "s_id" => Auth::user()->id,
                    "s_name" => Auth::user()->name,
                    "complete" => 0,
                    "mark" => $request->mark,
                    "time" => $dateinit->format('d M Y H:i')
                );
                $arr = json_decode($task->complete, true);
                array_push($arr, $str);
                $str = json_encode($arr);
                $task->count = $task->count + 1;
            }
            else
            {
                $str = array(
                    array(
                        "s_id" => Auth::user()->id,
                        "s_name" => Auth::user()->name,
                        "complete" => 0,
                        "mark" => $request->mark,
                        "time" => $dateinit->format('d M Y H:i')
                    )
                );
                $str = json_encode($str);
                $task->count = $task->count + 1;
            }
            $task->complete = $str;
        }

        $task->save();
        return Response::json(json_decode($task->complete));
    }

    // public function indexd(Request $request)
    // {
    //     $dataarray = array(
    //         'title' => "cvvfcvbfggbfg",
    //         'body' => "vdfvdfvdfgbcgf",
    //         'image' => "fvdfvf"
    //     );
    //     $token = 'e4SMGLBxQP78XbntJO_isR:APA91bHkfp7TZ08Hy07zJ2fbCaWNDNH6dcg1pY-Y5upaTgrARwHKtNevlzoa6E3ZlLL2vjXQM3jctifszvoiRGd6Lz1-SwqPc20hnbYTAi2g_gkD5IOqtftY3XV-n8RFhtDLPwltq9yy';
    //     $push = fcm_msg::push($title, $body, $dataarray, $token);
    //     return Response::json($push);
    // }
    public function chat(Request $request)
    {
        $chat = chatbox::where(['s_id' => Auth::user()->id, 'active' => '1'])
            ->get();
        $teacher = teacher::where(['active' => '1'])->select('id', 'name', 'photo')
            ->get();
        return view('chatbox', compact('chat', 'teacher'));
    }

    public function message(Request $request)
    {
        $dateinit = \Carbon\Carbon::parse($request->dateini);
        $datefim = \Carbon\Carbon::parse($request->datefim);
        $chat = new chatbox();
        $chat->by = 'student';
        $chat->s_id = Auth::user()->id;
        $chat->s_name = Auth::user()->name;
        $chat->s_contact = Auth::user()->mobile;
        $chat->t_id = $request->t_id;
        $chat->t_name = $request->t_name;
        $chat->message = $request->message;
        $chat->reply_id = $request->reply_id;
        $chat->reply_message = $request->reply_message;
        $chat->status = 'send';
        $chat->date = $dateinit->format('d M Y');
        $chat->time = $dateinit->format('h:i a');
        $chat->active = '1';
        $chat->save();
        return Response::json($chat);
    }
    public function delete(Request $request)
    {
        $chat = chatbox::where(['id' => $request->id, 'by' => 'student', 's_id' => Auth::user()
            ->id])
            ->update(['active' => 0]);
        return Response::json($chat);

    }

    public function update_status(Request $request)
    {
        $chat = chatbox::where(['by' => 'teacher', 's_id' => Auth::user()->id, 't_id' => $request->id, 'status' => 'send', 'active' => 1])
            ->update(['status' => 'seen']);
        return Response::json($chat);

    }
    public function new_msg(Request $request)
    {
        $chat = chatbox::where('id', '>', $request->get('id'))
            ->where(['s_id' => Auth::user()->id, 'active' => '1'])
            ->get();
        return Response::json($chat);

    }
    //--------------------------------------------------------------------------------classroom---------------------------------------------------------------
    public function join_classroom(Request $request)
    {

        return view('student.join_classroom');
    }

    public function live_classroom(Request $request)
    {
        $count = 0;
        $count_user = 0;
        $sts = 'pending';
        $count = classroom::where(['room_code' => $request->get('id') , 'active' => 1])
            ->count();
        if ($count > 0)
        {
            $classroom = classroom::where(['room_code' => $request->get('id') , 'active' => 1])
                ->get();
            foreach ($classroom as $class)
            {
                $cls = $class->status;
                $clid = $class->id;
                $clcode = $class->room_code;
            }
            if ($cls != 'Completed')
            {
                $chats = class_chat::where(['classroom_code' => $request->get('id') , 'active' => 1])
                    ->get();
                $count_user = class_user::where(['classroom_id' => $clid, 'classroom_code' => $clcode, 's_id' => Auth::user()->id, 'active' => 1])
                    ->count();
                if ($count_user > 0)
                {
                    $user = class_user::where(['classroom_id' => $clid, 'classroom_code' => $clcode, 's_id' => Auth::user()->id, 'active' => 1])
                        ->get();
                    foreach ($user as $class)
                    {
                        $status = $class->status;
                        $clsid = $class->id;
                    }
                    if ($status == 'pending')
                    {
                        event(new \App\Events\MyEvent(array(
                            'channel' => $clcode,
                            'event' => 'asktojoin',
                            'message' => array(
                                'id' => $clsid,
                                's_id' => Auth::user()->id,
                                's_name' => Auth::user()
                                    ->name
                            )
                        )));
                    }
                    elseif ($status == 'leave')
                    {
                        $t = time();
                        $user = new class_user();
                        $user->classroom_id = $clid;
                        $user->classroom_code = $clcode;
                        $user->s_id = Auth::user()->id;
                        $user->s_name = Auth::user()->name;
                        $user->join_time = $t;
                        $user->status = 'online';
                        $user->active = 1;
                        $user->save();
                        event(new \App\Events\MyEvent(array(
                            'channel' => $clcode,
                            'event' => 'userjoined',
                            'message' => array(
                                'id' => $user->id,
                                's_id' => Auth::user()->id,
                                's_name' => Auth::user()
                                    ->name
                            )
                        )));
                    }
                }
                else
                {
                    $t = time();
                    $user = new class_user();
                    $user->classroom_id = $clid;
                    $user->classroom_code = $clcode;
                    $user->s_id = Auth::user()->id;
                    $user->s_name = Auth::user()->name;
                    $user->join_time = $t;
                    $user->status = 'pending';
                    $user->active = 1;
                    $user->save();
                    event(new \App\Events\MyEvent(array(
                        'channel' => $clcode,
                        'event' => 'asktojoin',
                        'message' => array(
                            'id' => $user->id,
                            's_id' => Auth::user()->id,
                            's_name' => Auth::user()
                                ->name
                        )
                    )));
                }
                $user = class_user::where(['classroom_id' => $clid, 'classroom_code' => $clcode, 's_id' => Auth::user()->id, 'active' => 1])
                    ->get();
                return view('student.live_classroom', compact('classroom', 'chats', 'user'));
            }
            else
            {
                return redirect()
                    ->route('student-open_classroom', ['id' => $request->get('id') ]);
            }
        }
        else
        {
            $line = "Sorry! Live Classroom Not Found";
            return view('errors.classroom', compact('line'));
        }
    }

    public function live_classroom_mobile(Request $request)
    {
        $count = 0;
        $count = classroom::where(['room_code' => $request->get('id') , 'active' => 1])
            ->count();
        if ($count > 0)
        {
            $classroom = classroom::where(['room_code' => $request->get('id') , 'active' => 1])
                ->get();
            foreach ($classroom as $class)
            {
                $cls = $class->status;
            }
            if ($cls != 'Completed')
            {
                $chats = class_chat::where(['classroom_code' => $request->get('id') , 'active' => 1])
                    ->get();
                return view('student.live_classroom_mobile', compact('classroom', 'chats'));
            }
            else
            {
                return redirect()
                    ->route('student-open_classroom_mobile', ['id' => $request->get('id') ]);
            }
        }
        else
        {
            $line = "Sorry! Live Classroom Not Found";
            return view('errors.classroom', compact('line'));
        }
    }

    public function message_send_classroom(Request $request)
    {
        $t = time();
        $time = strftime('%I:%H %p', $t);
        $classroom = new class_chat();
        $classroom->classroom_id = $request->classroom_id;
        $classroom->classroom_code = $request->classroom_code;
        $classroom->classteacher_id = $request->classteacher_id;
        $classroom->by = 'student';
        $classroom->s_id = Auth::user()->id;
        $classroom->s_name = Auth::user()->name;
        $classroom->t_id = $request->classteacher_id;
        $classroom->t_name = $request->t_name;
        $classroom->send_time = $time;
        $classroom->msg = $request->msg;
        $classroom->msg_type = 'private';
        $classroom->active = 1;
        $classroom->save();
        event(new \App\Events\MyEvent(array(
            'channel' => $classroom->classroom_code,
            'event' => 'messagetoteacher',
            'message' => array(
                's_id' => $classroom->s_id,
                's_name' => $classroom->s_name,
                'send_time' => $classroom->send_time,
                'msg' => $classroom->msg
            )
        )));
        return Response::json($classroom);
    }

    public function classroom_event(Request $request)
    {
        event(new \App\Events\MyEvent(array(
            'channel' => $request->channel,
            'event' => $request->event,
            'message' => $request->message
        )));
        return 'done';
    }

    public function classroom_exit(Request $request)
    {
        $users = class_user::where(['classroom_id' => $request->classroom_id, 's_id' => Auth::user()->id, 'status' => 'online', 'active' => 1])
            ->get();
        foreach ($users as $user)
        {
            $t = time();
            $total = $t - $user->join_time;
            $use = class_user::where(['id' => $user->id, 'active' => 1])
                ->update(['status' => 'leave', 'exit_time' => $t, 'total_time' => $total]);
            event(new \App\Events\MyEvent(array(
                'channel' => $request->classroom_code,
                'event' => 'userleave',
                'message' => $user->id
            )));
        }
    }

    public function old_classroom(Request $request)
    {
        $teacher = teacher::where('active', 1)->orderBy('name', 'asc')
            ->select('id', 'name')
            ->get();
        if ($request->has('s') && $request->get('s') != '')
        {
            $search = $request->get('s');
            $users = classroom::where('title', 'like', '%' . $search . '%')->orWhere('description', 'like', '%' . $search . '%')->orWhere('room_code', 'like', '%' . $search . '%')->orWhere('start_time', 'like', '%' . $search . '%')->orWhere('t_name', 'like', '%' . $search . '%');
            if ($request->has('t') && $request->get('t') != '')
            {
                $users = $users->where(['t_id' => $request->get('t') , 'active' => '1'])
                    ->orderBy('id', 'desc')
                    ->paginate(10);
            }
            else
            {
                $users = $users->where('active', 1)
                    ->orderBy('id', 'desc')
                    ->paginate(10);
            }
            return view('student.old_classroom', compact('users', 'teacher'));
        }
        else
        {
            if ($request->has('t') && $request->get('t') != '')
            {
                $users = classroom::where(['t_id' => $request->get('t') , 'active' => '1'])
                    ->orderBy('id', 'desc')
                    ->paginate(10);
            }
            else
            {
                $users = classroom::where('active', 1)->orderBy('id', 'desc')
                    ->paginate(10);
            }
            return view('student.old_classroom', compact('users', 'teacher'));
        }
    }

    public function old_classroom_reload(Request $request)
    {
        $teacher = teacher::where('active', 1)->orderBy('name', 'asc')
            ->select('id', 'name')
            ->get();
        if ($request->has('s') && $request->get('s') != '')
        {
            $search = $request->get('s');
            $users = classroom::where('title', 'like', '%' . $search . '%')->orWhere('description', 'like', '%' . $search . '%')->orWhere('room_code', 'like', '%' . $search . '%')->orWhere('start_time', 'like', '%' . $search . '%')->orWhere('t_name', 'like', '%' . $search . '%')->whereNotIn('t_id', [Auth::user()->id]);
            if ($request->has('t') && $request->get('t') != '')
            {
                $users = $users->where(['t_id' => $request->get('t') , 'active' => '1'])
                    ->orderBy('id', 'desc')
                    ->paginate(10);
            }
            else
            {
                $users = $users->where('active', 1)
                    ->orderBy('id', 'desc')
                    ->paginate(10);
            }
            return view('student.old_classroom_reload', compact('users', 'teacher'));
        }
        else
        {
            if ($request->has('t') && $request->get('t') != '')
            {
                $users = classroom::where(['t_id' => $request->get('t') , 'active' => '1'])
                    ->orderBy('id', 'desc')
                    ->paginate(10);
            }
            else
            {
                $users = classroom::where('active', 1)->orderBy('id', 'desc')
                    ->paginate(10);
            }
            return view('student.old_classroom_reload', compact('users', 'teacher'));
        }
    }

    public function classroom_doc(Request $request)
    {
        $users = class_chat::where(['classroom_id' => $request->get('id') , 'msg_type' => 'doc'])
            ->get();
        return view('teacher.classroom_doc', compact('users'));
    }

    public function open_classroom(Request $request)
    {
        $count = 0;
        $count = classroom::where(['room_code' => $request->get('id') , 'active' => 1])
            ->count();
        if ($count > 0)
        {
            $classroom = classroom::where(['room_code' => $request->get('id') , 'active' => 1])
                ->get();
            foreach ($classroom as $class)
            {
                $cls = $class->status;
            }
            if ($cls == 'Completed')
            {
                return view('student.open_classroom', compact('classroom'));
            }
            else
            {
                $line = "Sorry! This is Upcoming or Live Classroom code";
                return view('errors.classroom', compact('line'));
            }
        }

        else
        {
            $line = "Sorry! Classroom Not Found";
            return view('errors.classroom', compact('line'));
        }
    }

    public function open_classroom_mobile(Request $request)
    {
        $count = 0;
        $count = classroom::where(['room_code' => $request->get('id') , 'active' => 1])
            ->count();
        if ($count > 0)
        {
            $classroom = classroom::where(['room_code' => $request->get('id') , 'active' => 1])
                ->get();
            foreach ($classroom as $class)
            {
                $cls = $class->status;
            }
            if ($cls == 'Completed')
            {
                return view('student.open_classroom_mobile', compact('classroom'));
            }
            else
            {
                $line = "Sorry! This is Upcoming or Live Classroom code";
                return view('errors.classroom', compact('line'));
            }
        }

        else
        {
            $line = "Sorry! Classroom Not Found";
            return view('errors.classroom', compact('line'));
        }
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------------
    //-----------------------------------------------------online test-----------------------------
    public function onlinetest(Request $request)
    {
        $a = Auth::user()->class;
        $b = Auth::user()->course;
        $c = Auth::user()->coursetype;
        $d = Auth::user()->groupid;
        $e = $a . $b . $c . $d;
        $wher = ['sid' => Auth::user()->id, 'active' => '1'];
        $results = result::where($wher)->get();
        if ($request->has('s') && $request->get('s') != '')
        {
            $studentsearch = $request->get('s');
            $where = ['cccgid' => $e, 'active' => '1', 'test_series' => NULL];
            $users = paper_link::where($where)->Where('paper', 'like', '%' . $studentsearch . '%')->orderBy('Created_at', 'desc')
                ->paginate(10);
            return view('student.online-test', compact('users', 'results'));
        }
        else
        {
            $where = ['cccgid' => $e, 'active' => '1', 'test_series' => NULL];
            $users = paper_link::where($where)->orderBy('Created_at', 'desc')
                ->paginate(10);
            return view('student.online-test', compact('users', 'results'));
        }
    }

    public function onlinetest_page(Request $request)
    {
        $a = Auth::user()->class;
        $b = Auth::user()->course;
        $c = Auth::user()->coursetype;
        $d = Auth::user()->groupid;
        $e = $a . $b . $c . $d;
        $wher = ['sid' => Auth::user()->id, 'active' => '1'];
        $results = result::where($wher)->get();
        if ($request->has('s') && $request->get('s') != '')
        {
            $studentsearch = $request->get('s');
            $where = ['cccgid' => $e, 'active' => '1', 'test_series' => NULL];
            $users = paper_link::where($where)->Where('paper', 'like', '%' . $studentsearch . '%')->orderBy('Created_at', 'desc')
                ->paginate(10);
            return view('student.online-test_reload', compact('users', 'results'));
        }
        else
        {
            $where = ['cccgid' => $e, 'active' => '1', 'test_series' => NULL];
            $users = paper_link::where($where)->orderBy('Created_at', 'desc')
                ->paginate(10);
            return view('student.online-test_reload', compact('users', 'results'));
        }
    }

    public function test_series_list(Request $request)
    {
        $a = Auth::user()->class;
        $b = Auth::user()->course;
        $c = Auth::user()->coursetype;
        $d = Auth::user()->groupid;
        $e = $a . $b . $c . $d;
        $wher = ['sid' => Auth::user()->id, 'active' => '1'];
        $results = result::where($wher)->get();
        if ($request->has('s') && $request->get('s') != '')
        {
            $studentsearch = $request->get('s');
            $where = ['cccgid' => $e, 'active' => '1'];
            $users = ts_folder_link::where($where)->Where('name', 'like', '%' . $studentsearch . '%')->orderBy('name', 'asc')
                ->paginate(10);
            return view('student.test_series_list', compact('users', 'results'));
        }
        else
        {
            $where = ['cccgid' => $e, 'active' => '1'];
            $users = ts_folder_link::where($where)->orderBy('name', 'asc')
                ->paginate(10);
            return view('student.test_series_list', compact('users', 'results'));
        }
    }

    public function test_series_list_page(Request $request)
    {
        $a = Auth::user()->class;
        $b = Auth::user()->course;
        $c = Auth::user()->coursetype;
        $d = Auth::user()->groupid;
        $e = $a . $b . $c . $d;
        $wher = ['sid' => Auth::user()->id, 'active' => '1'];
        $results = result::where($wher)->get();
        if ($request->has('s') && $request->get('s') != '')
        {
            $studentsearch = $request->get('s');
            $where = ['cccgid' => $e, 'active' => '1'];
            $users = ts_folder_link::where($where)->Where('name', 'like', '%' . $studentsearch . '%')->orderBy('name', 'asc')
                ->paginate(10);
            return view('student.test_series_list_reload', compact('users', 'results'));
        }
        else
        {
            $where = ['cccgid' => $e, 'active' => '1'];
            $users = ts_folder_link::where($where)->orderBy('name', 'asc')
                ->paginate(10);
            return view('student.test_series_list_reload', compact('users', 'results'));
        }
    }

    public function test_series(Request $request)
    {
        $a = Auth::user()->class;
        $b = Auth::user()->course;
        $c = Auth::user()->coursetype;
        $d = Auth::user()->groupid;
        $e = $a . $b . $c . $d;
        $wher = ['sid' => Auth::user()->id, 'active' => '1'];
        $results = result::where($wher)->get();
        if ($request->has('s') && $request->get('s') != '')
        {
            $studentsearch = $request->get('s');
            $where = ['cccgid' => $e, 'active' => '1', 'test_series' => 'true', 'folder_link_id' => $request->get("flid") ];
            $users = paper_link::where($where)->Where('paper', 'like', '%' . $studentsearch . '%')->orderBy('Created_at', 'desc')
                ->get();
            return view('student.test_series', compact('users', 'results'));
        }
        else
        {
            $where = ['cccgid' => $e, 'active' => '1', 'test_series' => 'true', 'folder_link_id' => $request->get("flid") ];
            $users = paper_link::where($where)->orderBy('Created_at', 'asc')
                ->get();
            return view('student.test_series', compact('users', 'results'));
        }
    }

    public function test_series_page(Request $request)
    {
        $a = Auth::user()->class;
        $b = Auth::user()->course;
        $c = Auth::user()->coursetype;
        $d = Auth::user()->groupid;
        $e = $a . $b . $c . $d;
        $wher = ['sid' => Auth::user()->id, 'active' => '1'];
        $results = result::where($wher)->get();
        if ($request->has('s') && $request->get('s') != '')
        {
            $studentsearch = $request->get('s');
            $where = ['cccgid' => $e, 'active' => '1', 'test_series' => 'true', 'folder_link_id' => $request->get("flid") ];
            $users = paper_link::where($where)->Where('paper', 'like', '%' . $studentsearch . '%')->orderBy('Created_at', 'desc')
                ->paginate(10);
            return view('student.test_series_reload', compact('users', 'results'));
        }
        else
        {
            $where = ['cccgid' => $e, 'active' => '1', 'test_series' => 'true', 'folder_link_id' => $request->get("flid") ];
            $users = paper_link::where($where)->orderBy('Created_at', 'asc')
                ->paginate(10);
            return view('student.test_series_reload', compact('users', 'results'));
        }
    }

    public function papershow(Request $request)
    {
        $questions = array();
        $answers = array();
        $question = new_question::where(['pqtypeid' => $request->get('id') . "X" . $request->get('type') , 'active' => '1'])
            ->get();
        $answer = new_answer::where(['pplsid' => $request->get('id') . "X" . $request->get('plid') . "X" . Auth::user()->id, 'active' => '1'])
            ->get();
        $type = $request->get('type');
        foreach ($question as $q)
        {
            $questions = json_decode($q->questions);
        }
        foreach ($answer as $q)
        {
            $answers = json_decode($q->answers);
        }
        return view('layout.studentpaperview', compact('questions', 'answers', 'type'));
    }

    public function old_papershow(Request $request)
    {
        $questions = question::where(['pid' => $request->get('id') , 'qtype' => $request->get('type') , 'active' => '1'])
            ->get();
        $answers = answer::where(['pplsid' => $request->get('id') . "X" . $request->get('plid') . "X" . Auth::user()->id, 'active' => '1'])
            ->get();
        $type = $request->get('type');

        return view('layout.studentpaperview', compact('questions', 'answers', 'type'));
    }
    //--------------------------------------------------------------paper section------------------------------
    public function instructions(Request $request)
    {
        $where = ['id' => $request->get('id') , 'active' => '1'];
        $users = paper_link::where($where)->get();
        foreach ($users as $value)
        {
            $pname = $value->paper;
            $plink = $value->plink;
            $type = $value->type;
            $id = $value->id;
        }
        if ($type == 'normal')
        {
            return view('student.nr_instructions', compact('pname', 'plink', 'id'));
        }
        else if ($type == 'custom')
        {
            return view('student.cm_instructions', compact('pname', 'plink', 'id'));
        }
        else if ($type == 'advanced')
        {
            return view('student.adv_instructions', compact('pname', 'plink', 'id'));
        }
    }

    public function testpaper(Request $request)
    {
        $where = ['id' => $request->get('plid') , 'plink' => $request->get('id') . '.blade.php', 'active' => '1'];
        $plinks = paper_link::where($where)->get();
        $pid = "";
        $type = "";
        $plid = "";
        $hardness = "";
        $rank = "";
        $cccgid = "";
        $paper = "";
        $questions = array();
        $answers = array();

        foreach ($plinks as $pepr)
        {
            $t = time();
            if (strtotime($pepr->publishtime) > $t)
            {
                return view('errors.404');
            }
        }
        foreach ($plinks as $val)
        {
            $pid = $val->pid;
            $type = $val->type;
            $plid = $val->id;
            $hardness = $val->hardness;
            $rank = $val->rank;
            $cccgid = $val->cccgid;
            $paper = $val->paper;
        }

        if ($type == 'normal')
        {
            $where1 = ['id' => $pid, 'active' => '1'];
            $papers = normal_paper::where($where1)->get();
        }
        elseif ($type == 'advanced')
        {
            $where2 = ['id' => $pid, 'active' => '1'];
            $papers = advance_paper::where($where2)->get();
        }
        elseif ($type == 'custom')
        {
            $where2 = ['id' => $pid, 'active' => '1'];
            $papers = custom_paper::where($where2)->get();
        }

        $where3 = ['pid' => $pid, 'qtype' => $type, 'active' => '1'];
        $question = new_question::where($where3)->orderBy('id', 'asc')
            ->get();
        foreach ($question as $q)
        {
            $questions = json_decode($q->questions);
        }
        $where4 = ['pplsid' => $pid . "X" . $plid . "X" . Auth::user()->id, 'active' => '1'];
        $answer = new_answer::where($where4)->orderBy('id', 'asc')
            ->get();
        foreach ($answer as $q)
        {
            $answers = json_decode($q->answers);
        }

        $where5 = ['pid' => $pid, 'plid' => $plid, 'sid' => Auth::user()->id, 'active' => '1'];
        $results = result::where($where5)->count();
        foreach ($papers as $v)
        {
            $TT = $v->TT;
        }
        $where6 = ['plsid' => $plid . "X" . Auth::user()->id, 'active' => '1'];
        $time = time_left::where($where6)->count();
        if ($time > 0)
        {
            $timelefts = time_left::where($where6)->get();
        }
        else
        {
            $time = new time_left();
            $time->plid = $plid;
            $time->sid = Auth::user()->id;
            $time->s_name = Auth::user()->name;
            $time->plsid = $plid . "X" . Auth::user()->id;
            $time->timeleft = $TT * 60;
            $time->active = 1;
            $time->save();
            $timelefts = time_left::where($where6)->get();
        }

        $link = 'Quiz/' . $request->get('id');
        $method = $request->get('indexdb');
        $random = rand(111111, 999999);
        return view($link, compact('papers', 'questions', 'answers', 'plinks', 'results', 'timelefts', 'method', 'random'));

    }

    public function timeleft(Request $requests)
    {
        $n = 0;
        $res = array();
        foreach (json_decode($requests->data) as $request)
        {
            if ($request->qid == 'total_time')
            {
                $where = ['plsid' => $request->plid . "X" . Auth::user()->id, 'active' => '1'];
                $user = time_left::where($where)->update(['timeleft' => $request->time]);
                $res[$n] = 'total_time';
            }
            $n++;
        }
        return Response::json($res);
    }
    /*public function saveanswer(Request $requests)
    {
        $n = 0;
        $res = array();
        foreach (json_decode($requests->data) as $request)
        {
            $where1 = ['qpqtypeid' => $request->qid . "X" . $request->pid . "X" . 'normal', 'active' => '1'];
            $q7 = "";
            $q2 = "";
            $questions = question::where($where1)->select('q1')
                ->get();
            foreach ($questions as $ques)
            {
                $q7 = $ques->q1;
                if ($ques->q1 == $request->ans)
                {
                    $q2 = 'Correct';
                }
                else
                {
                    $q2 = 'Incorrect';
                }
            }
            $where = ['qplsid' => $request->qid . "X" . $request->plid . "X" . $request->sid, 'active' => '1'];
            $ans = 0;
            $ans = answer::where($where)->count();
            if ($ans == 0)
            {
                $answer = new answer();
                $answer->qid = $request->qid;
                $answer->pid = $request->pid;
                $answer->plid = $request->plid;
                $answer->sid = $request->sid;
                $answer->pplsid = $request->pid . "X" . $request->plid . "X" . $request->sid;
                $answer->qtype = 'single';
                $answer->qplsid = $request->qid . "X" . $request->plid . "X" . $request->sid;
                $answer->qplsqtypeid = $request->qid . "X" . $request->plid . "X" . $request->sid . "X" . 'single';
                if ($q7 == 'Bonus')
                {
                    $answer->a1 = 'Bonus';
                    $answer->a7 = 'Bonus';
                    $answer->a8 = 'save';
                    $answer->answer = 'Correct';
                }
                else
                {
                    $answer->a1 = $request->ans;
                    $answer->a7 = $q7;
                    $answer->a8 = $request->type;
                    $answer->answer = $q2;
                }
                $answer->time_used = $request->time_used;
                $answer->active = 1;
                $answer->save();
                $res[$n] = $answer->qid;
            }
            else
            {
                if ($q7 == 'Bonus')
                {
                    $answer = answer::where($where)->update(['a1' => 'Bonus', 'a7' => 'Bonus', 'a8' => 'save', 'answer' => 'Correct', 'time_used' => $request->time_used]);
                    $res[$n] = $request->qid;
                }
                else
                {
                    $answer = answer::where($where)->update(['a1' => $request->ans, 'a7' => $q7, 'a8' => $request->type, 'answer' => $q2, 'time_used' => $request->time_used]);
                    $res[$n] = $request->qid;
                }
            }
            $n++;
        }
        return Response::json($res);
    
    }
    */
    public function saveanswer(Request $request)
    {
        $where = ['pplsid' => $request->pid . "X" . $request->plid . "X" . $request->sid, 'active' => '1'];
        $ans = 0;
        $ans = new_answer::where($where)->count();
        if ($ans == 0)
        {
            $answer = new new_answer();
            $answer->pid = $request->pid;
            $answer->plid = $request->plid;
            $answer->s_name = Auth::user()->name;
            $answer->sid = $request->sid;
            $answer->p_type = 'normal';
            $answer->answers = $request->data;
            $answer->pplsid = $request->pid . "X" . $request->plid . "X" . $request->sid;
            $answer->active = 1;
            $answer->save();

        }
        else
        {
            $answer = new_answer::where($where)->update(['answers' => $request->data]);
        }
        return Response::json(json_decode($request->data));
    }

    public function deleteanswer(Request $request)
    {
        $where = ['pplsid' => $request->plid . "X" . Auth::user()->id, 'active' => '1'];
        $ans = new_answer::where($where)->get();
        foreach ($ans as $k)
        {
            $answer = $k->answers;
        }
        return Response::json(json_decode($answer));
    }

    public function custom_saveanswer(Request $request)
    {
        $where = ['pplsid' => $request->pid . "X" . $request->plid . "X" . $request->sid, 'active' => '1'];
        $ans = 0;
        $ans = new_answer::where($where)->count();
        if ($ans == 0)
        {
            $answer = new new_answer();
            $answer->pid = $request->pid;
            $answer->plid = $request->plid;
            $answer->s_name = Auth::user()->name;
            $answer->sid = $request->sid;
            $answer->p_type = 'custom';
            $answer->answers = $request->data;
            $answer->pplsid = $request->pid . "X" . $request->plid . "X" . $request->sid;
            $answer->active = 1;
            $answer->save();

        }
        else
        {
            $answer = new_answer::where($where)->update(['answers' => $request->data]);
        }
        return Response::json(json_decode($request->data));
    }

    public function custom_ans_img_upload(Request $request)
    {

        if ($request->hasfile('image_file'))
        {
            $file = $request->file('image_file');
            $name = 'pl_' . $request->plid . '_qid_' . $request->qid . '_sid_' . Auth::user()->id . '_name_' . Auth::user()->name . '_time_' . time() . '.' . $file->getClientOriginalExtension();
            $path = base_path() . '/public_html/Quiz/custom_paper/' . $request->pname . '/response/' . $name;
            $image_resize = newImage::make($file->getRealPath());
            $image_resize->widen(1000, function ($constraint)
            {
                $constraint->upsize();
            });
            $image_resize->orientate();
            $image_resize->save($path);
            echo '/Quiz/custom_paper/' . $request->pname . '/response/' . $name;
        }

        //             }
        //  if(is_uploaded_file($_FILES['uploadFile']['tmp_name']))
        //  {
        //   sleep(1);
        //   $source_path = $_FILES['uploadFile']['tmp_name'];
        //   $target_path = 'upload/' . $_FILES['uploadFile']['name'];
        //   if(move_uploaded_file($source_path, $target_path))
        //   {
        //    echo '<img src="'.$target_path.'" class="img-thumbnail" width="300" height="250" />';
        //   }
        //  }
        // }
        // $where=['id'=>$request->id, 'active'=>'1'];
        //  $where1=['pqtypeid'=>$request->id.'Xcustom','active'=>'1'];
        //     $users = custom_paper::where($where)->select('pname')->get();
        //      $questions = new_question::where($where1)->get();
        //       $question=array();
        //       $id ='';
        //     foreach ($users as $user) {
        //      $pname=$user->pname;
        //       }
        //     foreach ($questions as $user) {
        //     $question =json_decode($user->questions);
        //      $id = $user->id;
        //       }
        //     $this->validate($request, [
        //             'filenames' => 'required'
        //     ]);
        //     if($request->hasfile('filenames'))
        //      {
        //         foreach($request->file('filenames') as $file)
        //         {
        //             $name=$file->getClientOriginalName();
        //             $file_name = pathinfo($name, PATHINFO_FILENAME);
        //             $path=base_path().'/public_html/Quiz/custom_paper/'.$pname.'/solution';
        //             $file->move($path,$name);
        //             $qpqtypeid=$file_name."X".$request->id."X".'custom';
        //                  $img='Quiz/custom_paper/'.$pname.'/solution/'.$name;
        //                  foreach ($question as $q) {
        //                   if($q->qpqtypeid==$qpqtypeid){$q->solimg=$img;$q->remember_token=time();}
        //                  }
        //         }
        //         $filea= new_question::where(['id'=>$id])->update(['questions' => json_encode($question)]);
        //      }
        //     return back()->with('success', 'uploaded Successfully');
        
    }

    public function custom_deleteanswer(Request $request)
    {
        $where = ['pplsid' => $request->plid . "X" . Auth::user()->id, 'active' => '1'];
        $ans = answer::where($where)->orderBy('qid', 'asc')
            ->get();
        return Response::json($ans);
    }

    //      function upload_anss(Request $request)
    //     {
    //       /*$ans = new_answer::where('active','1')->get();
    //       foreach ($ans as $a) {
    //         $paper_link = paper_link::where(['id'=>$a->plid])->get();
    //         foreach ($paper_link as $p) {
    //           $answer = new_answer::where(['id'=>$a->id])->update(['p_type'=>$p->type]);
    //         }
    //        } return Response::json('done');
    // */
    //        $result = result::where(['active'=>'1'])->get();
    //        foreach ($result as $rs) {
    //         $type =  $rs->type;
    //          $ans = answer::where(['pplsid'=>$rs->pid.'X'.$rs->plid.'X'.$rs->sid,'active'=>'1'])->get();
    //         $array = array();
    //         $i=0;
    //        foreach ($ans as $a) {
    //        $array[$i]=array("sid"=>$a->sid,"qid"=>$a->qid,"pid"=>$a->pid,"plid"=>$a->plid,"qtype"=>$a->qtype,"qplsid"=>$a->qplsid,"qplsqtypeid"=>$a->qplsqtypeid,"a1"=>$a->a1,"a2"=>$a->a2,"a3"=>$a->a3,"a4"=>$a->a4,"a5"=>$a->a5,"a6"=>$a->a6,"a7"=>$a->a7,"a8"=>$a->a8,"ans_type"=>$a->ans_type,"answer"=>$a->answer,"time_used"=>$a->time_used);
    //        $i++;
    //        }
    //        $arr=json_encode($array);
    //        $where = ['pplsid' => $rs->pid . "X" . $rs->plid . "X" . $rs->sid, 'active' => '1'];
    //         $ans = 0;
    //         $ans = new_answer::where($where)->count();
    //         if ($ans == 0)
    //         {
    //             $answer = new new_answer();
    //             $answer->pid = $rs->pid;
    //             $answer->plid = $rs->plid;
    //             $answer->sid = $rs->sid;
    //             $answer->p_type = $type;
    //             $answer->answers = $arr;
    //             $answer->pplsid = $rs->pid . "X" . $rs->plid . "X" . $rs->sid;
    //             $answer->active = 1;
    //             $answer->save();
    //         }
    //         else
    //         {
    //             $answer = new_answer::where($where)->update(['p_type'=>$type,'answers' => $arr]);
    //         }
    //        }
    //         return Response::json('done');
    //     }
    // function upload_quesss(Request $request)
    //     {
    //        $result = advance_paper::where(['active'=>'1'])->get();
    //        foreach ($result as $rs) {
    //          $ans = question::where(['pid'=>$rs->id,'qtype'=>'advanced','active'=>'1'])->get();
    //         $array = array();
    //         $i=0;
    //        foreach ($ans as $a) {
    //        $array[$i]=array("qid"=>$a->qid,"pid"=>$a->pid,"qtype"=>$a->qtype,"qpid"=>$a->qpid,"qpqtypeid"=>$a->qpqtypeid,"type"=>$a->type,"quesimg"=>$a->quesimg,"solimg"=>$a->solimg,"q1"=>$a->q1,"q2"=>$a->q2,"q3"=>$a->q3,"q4"=>$a->q4,"q5"=>$a->q5,"q6"=>$a->q6,"q7"=>$a->q7,"q8"=>$a->a8,"remember_token"=>$a->remember_token);
    //        $i++;
    //        }
    //        $arr=json_encode($array);
    //        $where = ['pqtypeid' => $rs->id . "Xadvanced", 'active' => '1'];
    //         $ans = 0;
    //         $ans = new_question::where($where)->count();
    //         if ($ans == 0)
    //         {
    //             $answer = new new_question();
    //             $answer->pid = $rs->id;
    //             $answer->qtype = "advanced";
    //             $answer->pqtypeid = $rs->id . "Xadvanced";
    //             $answer->questions = $arr;
    //             $answer->active = 1;
    //             $answer->save();
    //         }
    //         else
    //         {
    //             $answer = new_question::where($where)->update(['questions' => $arr]);
    //         }
    //        }
    //         return Response::json('done');
    //     }
    

    public function submit_result(Request $request)
    {
        $timeleft = 0;
        $tt = time_left::where(['plsid' => $request->plid . "X" . Auth::user()->id, 'active' => '1'])
            ->select('timeleft')
            ->get();
        foreach ($tt as $tq)
        {
            $timeleft = intdiv($tq->timeleft, 60);
        }
        $date = new \DateTime();
        $timer = date_format($date, 'Y-m-d H:i:s');
        $time = $request->TT - $timeleft;
        $result = new result();
        $result->pid = $request->pid;
        $result->plid = $request->plid;
        $result->sid = Auth::user()->id;
        $result->type = 'normal';
        $result->classid = Auth::user()->class;
        $result->courseid = Auth::user()->course;
        $result->coursetypeid = Auth::user()->coursetype;
        $result->groupid = Auth::user()->groupid;
        $result->cccgid = Auth::user()->class . "X" . Auth::user()->course . "X" . Auth::user()->coursetype . "X" . Auth::user()->groupid;
        $result->name = Auth::user()->name;
        $result->paper = $request->pname;
        $result->timer = $timer;
        $result->lefttime = $time;
        $result->blurtime = $request->blurtime;
        $result->PQ = $request->PQN;
        $result->CQ = $request->CQN - $request->PQN;
        $result->MQ = $request->MQN - $request->CQN;
        $result->BQ = $request->BQN - $request->MQN;
        $result->total_marks = $request->total_marks;
        $result->totalQ = $request->totalQ;
        $result->totalA = $request->totalA;
        $result->totalC = $request->totalC;
        $result->totalW = $request->totalW;
        $result->totalS = $request->totalS;
        $result->totalCinP = $request->CinP;
        $result->totalWinP = $request->WinP;
        $result->totalSinP = $request->MinP;
        $result->totalCinC = $request->CinC;
        $result->totalWinC = $request->WinC;
        $result->totalSinC = $request->MinC;
        $result->totalCinM = $request->CinM;
        $result->totalWinM = $request->WinM;
        $result->totalSinM = $request->MinM;
        $result->totalCinB = $request->CinB;
        $result->totalWinB = $request->WinB;
        $result->totalSinB = $request->MinB;
        $result->active = 1;
        $result->save();
        $tts = time_left::where(['plsid' => $request->plid . "X" . Auth::user()->id, 'active' => '1'])
            ->update(['result' => 'Submited']);
        //---------------------------------------------------------------------Notification section--------------------------------------------
        $body = "Your Test " . $request->pname . " is submitted successfully.
Your Score is : " . $request->totalS . "/" . $request->total_marks;
        if ($result->PQ != 0)
        {
            $body = $body . "
Physics         :  " . $request->MinP . "   Marks";
        }
        if ($result->CQ != 0)
        {
            $body = $body . "
Chemistry     :  " . $request->MinC . "   Marks";
        }
        if ($result->MQ != 0)
        {
            $body = $body . "
Mathmatics :  " . $request->MinM . "   Marks";
        }
        if ($result->BQ != 0)
        {
            $body = $body . "
Biology          :  " . $request->MinB . "   Marks";
        }
        $acd_id = Auth::user()->acd_id;
        $acd_name = Auth::user()->acd_name;
        $notification_type = 'right_icon_long';
        $title = 'Result Generated Successfully.';
        $body = $body;
        $title_long = 'Result Generated Successfully.';
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
        $summary = "Result Submit";
        $icon = asset('') . env('NOTI_ICON');
        $image = asset('') . env('NOTI_ICON');

        $browser_token = array();
        $br_tk = 0;
        $app_tk = 0;
        $app_token = array();
        $url = 'https://fcm.googleapis.com/fcm/send';

        $use = token::where(['user_id' => Auth::user()->id, 'user_type' => 'student', 'active' => '1'])
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
                    "noti_id" => rand(3, 8) ,
                    "channel_id" => "Notification",
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
            $results = curl_exec($ch);
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
                    "noti_id" => rand(3, 8) ,
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
            $results = curl_exec($ch);
            curl_close($ch);
        }
        return Response::json($result->id);
    }

    public function custom_submit_result(Request $request)
    {
        $timeleft = 0;
        $tt = time_left::where(['plsid' => $request->plid . "X" . Auth::user()->id, 'active' => '1'])
            ->select('timeleft')
            ->get();
        foreach ($tt as $tq)
        {
            $timeleft = intdiv($tq->timeleft, 60);
        }
        $date = new \DateTime();
        $timer = date_format($date, 'Y-m-d H:i:s');
        $time = $request->TT - $timeleft;
        $result = new result();
        $result->pid = $request->pid;
        $result->plid = $request->plid;
        $result->sid = Auth::user()->id;
        $result->type = 'custom';
        $result->classid = Auth::user()->class;
        $result->courseid = Auth::user()->course;
        $result->coursetypeid = Auth::user()->coursetype;
        $result->groupid = Auth::user()->groupid;
        $result->cccgid = Auth::user()->class . "X" . Auth::user()->course . "X" . Auth::user()->coursetype . "X" . Auth::user()->groupid;
        $result->name = Auth::user()->name;
        $result->paper = $request->pname;
        $result->timer = $timer;
        $result->lefttime = $time;
        $result->blurtime = $request->blurtime;
        $result->total_marks = $request->total_marks;
        $result->totalQ = $request->totalQ;
        $result->totalA = $request->totalA;
        $result->totalC = $request->totalC;
        $result->totalW = $request->totalW;
        $result->totalP = $request->totalP;
        $result->totalS = $request->totalS;
        $result->custom_structure = json_encode($request->custom_structure);
        $result->PQ = 0;
        $result->CQ = 0;
        $result->MQ = 0;
        $result->BQ = 0;
        $result->totalCinP = 0;
        $result->totalWinP = 0;
        $result->totalSinP = 0;
        $result->totalCinC = 0;
        $result->totalWinC = 0;
        $result->totalSinC = 0;
        $result->totalCinM = 0;
        $result->totalWinM = 0;
        $result->totalSinM = 0;
        $result->totalCinB = 0;
        $result->totalWinB = 0;
        $result->totalSinB = 0;
        $result->active = 1;
        $result->save();
        $tts = time_left::where(['plsid' => $request->plid . "X" . Auth::user()->id, 'active' => '1'])
            ->update(['result' => 'Submited']);
        //---------------------------------------------------------------------Notification section--------------------------------------------
        $body = "Your Test " . $request->pname . " is submitted successfully.
Your Score is : " . $request->totalS . "/" . $request->total_marks;
        foreach (json_decode($result->custom_structure) as $p)
        {
            if ($p->question > 0)
            {
                $body = $body . "
" . $p->subject . "  :  " . $p->totalS . "/" . $p->total_marks;
            }
        }
        $acd_id = Auth::user()->acd_id;
        $acd_name = Auth::user()->acd_name;
        $notification_type = 'right_icon_long';
        $title = 'Result Generated Successfully.';
        $body = $body;
        $title_long = 'Result Generated Successfully.';
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
        $summary = "Result Submit";
        $icon = asset('') . env('NOTI_ICON');
        $image = asset('') . env('NOTI_ICON');

        $browser_token = array();
        $br_tk = 0;
        $app_tk = 0;
        $app_token = array();
        $url = 'https://fcm.googleapis.com/fcm/send';

        $use = token::where(['user_id' => Auth::user()->id, 'user_type' => 'student', 'active' => '1'])
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
                    "noti_id" => rand(3, 8) ,
                    "channel_id" => "Notification",
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
            $results = curl_exec($ch);
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
                    "noti_id" => rand(3, 8) ,
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
            $results = curl_exec($ch);
            curl_close($ch);
        }
        return Response::json($result->id);
    }

    public function adv_saveanswer(Request $request)
    {
        $where = ['pplsid' => $request->pid . "X" . $request->plid . "X" . $request->sid, 'active' => '1'];
        $ans = 0;
        $ans = new_answer::where($where)->count();
        if ($ans == 0)
        {
            $answer = new new_answer();
            $answer->pid = $request->pid;
            $answer->plid = $request->plid;
            $answer->s_name = Auth::user()->name;
            $answer->sid = $request->sid;
            $answer->p_type = 'advanced';
            $answer->answers = $request->data;
            $answer->pplsid = $request->pid . "X" . $request->plid . "X" . $request->sid;
            $answer->active = 1;
            $answer->save();

        }
        else
        {
            $answer = new_answer::where($where)->update(['answers' => $request->data]);
        }
        return Response::json(json_decode($request->data));
    }

    public function adv_deleteanswer(Request $request)
    {
        $where = ['pplsid' => $request->plid . "X" . Auth::user()->id, 'active' => '1'];
        $ans = answer::where($where)->orderBy('qid', 'asc')
            ->get();
        return Response::json($ans);
    }

    public function adv_submit_result(Request $request)
    {
        $timeleft = 0;
        $tt = time_left::where(['plsid' => $request->plid . "X" . Auth::user()->id, 'active' => '1'])
            ->select('timeleft')
            ->get();
        foreach ($tt as $tq)
        {
            $timeleft = intdiv($tq->timeleft, 60);
        }
        $date = new \DateTime();
        $timer = date_format($date, 'Y-m-d H:i:s');
        $time = $request->TT - $timeleft;
        $result = new result();
        $result->pid = $request->pid;
        $result->plid = $request->plid;
        $result->sid = Auth::user()->id;
        $result->type = 'advanced';
        $result->classid = Auth::user()->class;
        $result->courseid = Auth::user()->course;
        $result->coursetypeid = Auth::user()->coursetype;
        $result->groupid = Auth::user()->groupid;
        $result->cccgid = Auth::user()->class . "X" . Auth::user()->course . "X" . Auth::user()->coursetype . "X" . Auth::user()->groupid;
        $result->name = Auth::user()->name;
        $result->paper = $request->pname;
        $result->timer = $timer;
        $result->lefttime = $time;
        $result->blurtime = $request->blurtime;
        $result->PQ = $request->PQN;
        $result->CQ = $request->CQN - $request->PQN;
        $result->MQ = $request->MQN - $request->CQN;
        $result->BQ = 0;
        $result->total_marks = $request->total_marks;
        $result->totalQ = $request->totalQ;
        $result->totalA = $request->totalA;
        $result->totalC = $request->totalC;
        $result->totalW = $request->totalW;
        $result->totalS = $request->totalS;
        $result->totalCinP = $request->CinP;
        $result->totalWinP = $request->WinP;
        $result->totalSinP = $request->MinP;
        $result->totalCinC = $request->CinC;
        $result->totalWinC = $request->WinC;
        $result->totalSinC = $request->MinC;
        $result->totalCinM = $request->CinM;
        $result->totalWinM = $request->WinM;
        $result->totalSinM = $request->MinM;
        $result->totalCinB = 0;
        $result->totalWinB = 0;
        $result->totalSinB = 0;
        $result->active = 1;
        $result->save();
        $tts = time_left::where(['plsid' => $request->plid . "X" . Auth::user()->id, 'active' => '1'])
            ->update(['result' => 'Submited']);

        //---------------------------------------------------------------------Notification section--------------------------------------------
        $body = "Your Test " . $request->pname . " is submitted successfully.
Your Score is : " . $request->totalS . "/" . $request->total_marks;
        if ($result->PQ != 0)
        {
            $body = $body . "
Physics         :  " . $request->MinP . "   Marks";
        }
        if ($result->CQ != 0)
        {
            $body = $body . "
Chemistry     :  " . $request->MinC . "   Marks";
        }
        if ($result->MQ != 0)
        {
            $body = $body . "
Mathmatics :  " . $request->MinM . "   Marks";
        }
        if ($result->BQ != 0)
        {
            $body = $body . "
Biology          :  " . $request->MinB . "   Marks";
        }
        $acd_id = Auth::user()->acd_id;
        $acd_name = Auth::user()->acd_name;
        $notification_type = 'right_icon_long';
        $title = 'Result Generated Successfully.';
        $body = $body;
        $title_long = 'Result Generated Successfully.';
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
        $summary = "Result Submit";
        $icon = asset('') . env('NOTI_ICON');
        $image = asset('') . env('NOTI_ICON');

        $browser_token = array();
        $br_tk = 0;
        $app_tk = 0;
        $app_token = array();
        $url = 'https://fcm.googleapis.com/fcm/send';

        $use = token::where(['user_id' => Auth::user()->id, 'user_type' => 'student', 'active' => '1'])
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
                    "noti_id" => rand(3, 8) ,
                    "channel_id" => "Notification",
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
            $results = curl_exec($ch);
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
                    "noti_id" => rand(3, 8) ,
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
            $results = curl_exec($ch);
            curl_close($ch);
        }
        return Response::json($result->id);
    }

    public function paper_sockets(Request $request)
    {
        event(new \App\Events\MyEvent(array(
            'channel' => $request->get('channel') ,
            'event' => $request->get('event') ,
            'message' => array(
                'plid' => $request->get('plid') ,
                'sid' => $request->get('sid') ,
                'sname' => $request->get('sname') ,
                'timeleft' => $request->get('timeleft') ,
                'entry' => $request->get('entry')
            )
        )));
        return Response::json('done');
    }

    //--------------------------------------------------------------results----------------------------
    

    public function resultshow(Request $request)
    {
        $where = ['id' => $request->get('id') , 'sid' => Auth::user()->id, 'active' => '1'];
        $users = result::where($where)->orderBy('id', 'desc')
            ->paginate(10);
        return view('layout.resultview', compact('users'));
    }

    public function results(Request $request)
    {
        if ($request->has('s') && $request->get('s') != '')
        {
            $studentsearch = $request->get('s');
            $where = ['sid' => Auth::user()->id, 'active' => '1'];
            $users = result::where($where)->Where('paper', 'like', '%' . $studentsearch . '%')->orWhere('created_at', 'like', '%' . $studentsearch . '%')->orderBy('id', 'desc')
                ->paginate(10);
            return view('student.results', compact('users'));
        }
        else
        {
            $where = ['sid' => Auth::user()->id, 'active' => '1'];
            $users = result::where($where)->orderBy('id', 'desc')
                ->paginate(10);
            return view('student.results', compact('users'));
        }

    }
    public function results_page(Request $request)
    {
        if ($request->has('s') && $request->get('s') != '')
        {
            $studentsearch = $request->get('s');
            $where = ['sid' => Auth::user()->id, 'active' => '1'];
            $users = result::where($where)->Where('paper', 'like', '%' . $studentsearch . '%')->orWhere('created_at', 'like', '%' . $studentsearch . '%')->orderBy('id', 'desc')
                ->paginate(10);
            return view('student.results_reload', compact('users'));
        }
        else
        {
            $where = ['sid' => Auth::user()->id, 'active' => '1'];
            $users = result::where($where)->orderBy('id', 'desc')
                ->paginate(10);
            return view('student.results_reload', compact('users'));
        }

    }

    function result_analysis(Request $request)
    {
        $where = ['id' => $request->get('id') , 'active' => '1'];
        $results = result::where($where)->get();
        foreach ($results as $key => $user)
        {
            $id = $user->pid . 'X' . $user->plid . 'X' . Auth::user()->id;
            $pid = $user->pid;
            $ptype = $user->type;
        }
        $answers = array();
        $answer = new_answer::where('pplsid', $id)->get();
        foreach ($answer as $k)
        {
            $answers = json_decode($k->answers);
        }
        if ($ptype == 'advanced')
        {
            $papers = advance_paper::where('id', $pid)->get();
        }
        else if ($ptype == 'custom')
        {
            $papers = custom_paper::where('id', $pid)->get();
        }
        else
        {
            $papers = normal_paper::where('id', $pid)->get();
        }
        return view('student.result_analysis', compact('results', 'answers', 'papers'));
    }

    //---------------------------------------------------settings-----------------
    public function settings()
    {

        return view('student.settings');
    }
    public function changepassword(Request $request)
    {

        $old = Auth::user()->password;
        if (Hash::check($request->old, $old))
        {
            $inv = student::where('id', Auth::user()->id)
                ->update(['password' => Hash::make($request->new) ]);
            return Response::json($inv);
        }
        else
        {
            return Response::json(array(
                'errors' => 'fail'
            ));
        }
    }

    public function dpp(Request $request)
    {
        $a = Auth::user()->class;
        $b = Auth::user()->course;
        $c = Auth::user()->coursetype;
        $d = Auth::user()->groupid;
        $e = $a . $b . $c . $d;
        if ($request->has('sub') && $request->get('sub') != '')
        {
            $where = ['cccgid' => $e, 'subject' => $request->get('sub') , 'active' => '1'];
            $users = dpp_link::where($where)->orderBy('publish_time', 'desc')
                ->paginate(20);
            return view('student.dpp', compact('users'));
        }
    }

    public function dpp_page(Request $request)
    {
        $a = Auth::user()->class;
        $b = Auth::user()->course;
        $c = Auth::user()->coursetype;
        $d = Auth::user()->groupid;
        $e = $a . $b . $c . $d;
        if ($request->has('sub') && $request->get('sub') != '')
        {
            $where = ['cccgid' => $e, 'subject' => $request->get('sub') , 'active' => '1'];
            $users = dpp_link::where($where)->orderBy('publish_time', 'desc')
                ->paginate(20);
            return view('student.dpp_reload', compact('users'));
        }
    }

    public function video_lecture(Request $request)
    {
        $users = lecture::where(['id' => $request->get('id') , 'active' => '1'])
            ->get();
        return view('student.video_lecture', compact('users'));
    }
    public function mobile_video_lecture(Request $request)
    {
        $users = lecture::where(['id' => $request->get('id') , 'active' => '1'])
            ->get();
        return view('student.video_lecture_mobile', compact('users'));
    }
    public function lectures(Request $request)
    {
        if (!$request->has('fid') && !$request->has('sfid'))
        {
            $type = 'subject';
            $users = lecture_link::where(['cccgid' => Auth::user()->class . Auth::user()->course . Auth::user()->coursetype . Auth::user()->groupid, 'active' => '1'])
                ->orderBy('folder_id', 'asc')
                ->get();
            return view('student.lectures', compact('users', 'type'));
        }
        if ($request->has('fid') && !$request->has('sfid'))
        {
            $type = 'topic';
            $users = lecture_link::where(['folder_id' => $request->fid, 'cccgid' => Auth::user()->class . Auth::user()->course . Auth::user()->coursetype . Auth::user()->groupid, 'active' => '1'])
                ->orderBy('subfolder_id', 'asc')
                ->get();
            return view('student.lectures', compact('users', 'type'));
        }
        if ($request->has('fid') && $request->has('sfid'))
        {
            $type = 'lecture';
            $users = lecture_link::where(['folder_id' => $request->fid, 'subfolder_id' => $request->sfid, 'cccgid' => Auth::user()->class . Auth::user()->course . Auth::user()->coursetype . Auth::user()->groupid, 'active' => '1'])
                ->orderBy('id', 'asc')
                ->get();
            return view('student.lectures', compact('users', 'type'));
        }
    }

}

