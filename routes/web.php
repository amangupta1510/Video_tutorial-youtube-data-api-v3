<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// route to login and logout

     Route::get('/','HomeController@home')->name('home');
     Route::POST('/signup','HomeController@sign_up')->name('home-sign_up');
     Route::POST('/otpcheck','HomeController@otpcheck')->name('home-otpcheck');
     Route::POST('/forgotpassword','HomeController@forgotpassword')->name('home-forgotpassword');
     Route::POST('/forgotpasswordchange','HomeController@forgotpasswordchange')->name('home-forgotpasswordchange');
     Route::get('/tokendkcs','HomeController@token')->name('home-token');

     Route::get('/faq','HomeController@faq')->name('faq');
     Route::get('/branches','HomeController@branches')->name('branches');
     Route::get('/gallery','HomeController@gallery')->name('gallery');
     Route::get('/form','HomeController@form')->name('form');
     Route::get('/form1','HomeController@form1')->name('form1');
     Route::get('/regular_courses','HomeController@regular_courses')->name('regular_courses'); 
     Route::get('/foundation_courses','HomeController@foundation_courses')->name('foundation_courses');
     Route::get('/about_us', 'HomeController@about_us')->name('about_us');
     Route::get('/online_exam_portal', 'HomeController@online_exam_portal')->name('online_exam_portal'); 
     Route::get('/pricing', 'HomeController@pricing')->name('pricing'); 
     Route::get('/demo', 'HomeController@demo')->name('demo'); 
     Route::get('/contact', 'HomeController@contact')->name('contact'); 
     Route::post('/ThankYou', 'HomeController@contact_us')->name('ThankYou'); 
     Route::get('/offers','OffersController@offers')->name('offers');



 Route::prefix('user')->group(function() {
     //Route::get('/upload_anss', 'studentcontroller@upload_quesss')->name('student-saveanswer');
     Route::get('login', 'Auth\StudentLoginController@showLoginForm')->name('student-form');
     Route::post('login', 'Auth\StudentLoginController@attemptlogin')->name('student-login');
     Route::post('logout', 'Auth\StudentLoginController@logout')->name('student-logout');
     Route::get('/dashboard', 'studentcontroller@index')->name('student-dashboard');
     Route::get('/', 'HomeController@indexa');

//------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------web sockets---------------------------------------

     Route::get('/sockets_paper_controller', 'studentcontroller@paper_sockets')->name('student-paper_sockets');


     Route::get('/lectures', 'studentcontroller@lectures')->name('student-lectures');
     Route::get('/video_lecture', 'studentcontroller@video_lecture')->name('student-video_lecture');
     Route::get('/mobile_video_lecture', 'studentcontroller@mobile_video_lecture')->name('student-mobile_video_lecture');
  });


  Route::prefix('/admin')->group(function() {
      Route::get('login', 'Auth\AdminLoginController@showLoginForm')->name('admin-form');
      Route::post('login', 'Auth\AdminLoginController@attemptlogin')->name('admin-login');
      Route::post('logout', 'Auth\AdminLoginController@logout')->name('admin-logout');
      Route::get('/dashboard', 'AdminController@index')->name('admin-dashboard');
      Route::get('/profile', 'AdminController@profile')->name('admin-profile');
      Route::get('/', 'HomeController@indexb');

     //-----------------------------------------------------Video Lecture-------------------------------------------------------
     Route::get('/lectures', 'AdminVideoLectureController@lectures')->name('admin-lectures');
     Route::get('/lecture_links', 'AdminVideoLectureController@lecture_links')->name('admin-lecture_links');
     Route::get('/video_lecture', 'AdminVideoLectureController@video_lecture')->name('admin-video_lecture');
     Route::get('/mobile_video_lecture', 'AdminVideoLectureController@mobile_video_lecture')->name('admin-mobile_video_lecture');
     Route::POST('/add_lecture_folder', 'AdminVideoLectureController@add_lecture_folder')->name('admin-add_lecture_folder');
     Route::POST('/edit_lecture_folder', 'AdminVideoLectureController@edit_lecture_folder')->name('admin-edit_lecture_folder');
     Route::POST('/delete_lecture_folder', 'AdminVideoLectureController@delete_lecture_folder')->name('admin-delete_lecture_folder');
     Route::POST('/add_lecture_subfolder', 'AdminVideoLectureController@add_lecture_subfolder')->name('admin-add_lecture_subfolder');
     Route::POST('/edit_lecture_subfolder', 'AdminVideoLectureController@edit_lecture_subfolder')->name('admin-edit_lecture_subfolder');
     Route::POST('/delete_lecture_subfolder', 'AdminVideoLectureController@delete_lecture_subfolder')->name('admin-delete_lecture_subfolder');
     Route::POST('/add_lecture', 'AdminVideoLectureController@add_lecture')->name('admin-add_lecture');
     Route::POST('/edit_lecture', 'AdminVideoLectureController@edit_lecture')->name('admin-edit_lecture');
     Route::POST('/delete_lecture', 'AdminVideoLectureController@delete_lecture')->name('admin-delete_lecture');
     Route::POST('/add_lecture_link', 'AdminVideoLectureController@add_lecture_link')->name('admin-add_lecture_link');
     Route::POST('/edit_lecture_link', 'AdminVideoLectureController@edit_lecture_link')->name('admin-edit_lecture_link');
     Route::POST('/delete_lecture_link', 'AdminVideoLectureController@delete_lecture_link')->name('admin-delete_lecture_link');
  });
