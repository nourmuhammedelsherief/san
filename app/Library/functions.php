<?php

use App\Models\RestaurantSensitivity;
use App\Models\Teacher\StudentRate;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Facades\FCM;
use App\Models\Setting;
//use FCM;

function domain()
{
    return 'localhost:8000';
}

function validateRules($errors, $rules)
{

    $error_arr = [];

    foreach ($rules as $key => $value) {

        if ($errors->get($key)) {

            array_push($error_arr, array('key' => $key, 'value' => $errors->first($key)));
        }
    }

    return $error_arr;
}

function randNumber($length)
{

    $seed = str_split('0123456789');

    shuffle($seed);

    $rand = '';

    foreach (array_rand($seed, $length) as $k) $rand .= $seed[$k];

    return $rand;
}

function generateApiToken($userId, $length)
{

    $seed = str_split('abcdefghijklmnopqrstuvwxyz' . 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . '0123456789');

    shuffle($seed);

    $rand = '';

    foreach (array_rand($seed, $length) as $k) $rand .= $seed[$k];

    return $userId * $userId . $rand;
}

function UploadImage($inputRequest, $prefix, $folderNam)
{
    $image = time() . '' . rand(11111, 99999) . '.' . $inputRequest->getClientOriginalExtension();
    $destinationPath = public_path('/' . $folderNam);
    $img = Image::make($inputRequest->getRealPath());
    $img->resize(900, 900, function ($constraint) {
        $constraint->aspectRatio();
    })->save($destinationPath . '/' . $image);
    return $image ? $image : false;
}

function UploadVideo($file)
{
    if ($file) {
        $filename = time() . '' . rand(11111, 99999) . '.' . $file->getClientOriginalExtension();
        $path = public_path() . '/uploads/videos';
        $file->move($path, $filename);
        return $filename;
    }
}

function UploadVideoEdit($file, $old)
{
    if ($old) {
        @unlink(public_path('/uploads/videos/' . $old));
    }
    if ($file) {
        $filename = time() . '' . rand(11111, 99999) . '.' . $file->getClientOriginalExtension();
        $path = public_path() . '/uploads/videos';
        $file->move($path, $filename);
        return $filename;
    }
}

function UploadImageEdit($inputRequest, $prefix, $folderNam, $oldImage)
{
    @unlink(public_path('/' . $folderNam . '/' . $oldImage));
    $image = time() . '' . rand(11111, 99999) . '.' . $inputRequest->getClientOriginalExtension();
    $destinationPath = public_path('/' . $folderNam);
    $img = Image::make($inputRequest->getRealPath());
    $img->resize(900, 900, function ($constraint) {
        $constraint->aspectRatio();
    })->save($destinationPath . '/' . $image);
    return $image ? $image : false;
}

####### Check Payment Status ######
function MyFatoorahStatus($api, $PaymentId)
{
    // dd($PaymentId);
    $token = $api;
    $basURL = "https://apitest.myfatoorah.com";
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "$basURL/v2/GetPaymentStatus",
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{\"Key\": \"$PaymentId\",\"KeyType\": \"PaymentId\"}",
        CURLOPT_HTTPHEADER => array("Authorization: Bearer $token", "Content-Type: application/json"),
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        return $err;
    } else {
        return $response;
    }
}

// ===============================  MyFatoorah public  function  =========================
function MyFatoorah($api, $userData)
{
    // dd($userData);
    $token = $api;
    $basURL = "https://apitest.myfatoorah.com";
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "$basURL/v2/ExecutePayment",
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $userData,
        CURLOPT_HTTPHEADER => array("Authorization: Bearer $token", "Content-Type: application/json"),
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        return $err;
    } else {
        return $response;
    }
}

/**
 * calculate the distance between tow places on the earth
 *
 * @param latitude $latitudeFrom
 * @param longitude $longitudeFrom
 * @param latitude $latitudeTo
 * @param longitude $longitudeTo
 * @return double distance in KM
 */
function distanceBetweenTowPlaces($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo)
{
    $long1 = deg2rad($longitudeFrom);
    $long2 = deg2rad($longitudeTo);
    $lat1 = deg2rad($latitudeFrom);
    $lat2 = deg2rad($latitudeTo);
    //Haversine Formula
    $dlong = $long2 - $long1;
    $dlati = $lat2 - $lat1;
    $val = pow(sin($dlati / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($dlong / 2), 2);
    $res = 2 * asin(sqrt($val));
    $radius = 6367.756;
    return ($res * $radius);
}


/**
 *  Taqnyat sms to send message
 */
function taqnyatSms($msgBody, $reciver)
{
    $setting = Setting::find(1);
    $bearer = $setting->bearer_token;
    $sender = $setting->sender_name;
    $taqnyt = new TaqnyatSms($bearer);
    $body = $msgBody;
    $recipients = $reciver;
    $message = $taqnyt->sendMsg($body, $recipients, $sender);
    return $message;
}

function sendNotification($firebaseToken , $title , $body , $photo=null)
{
    $SERVER_API_KEY = 'AAAAMPW1SSg:APA91bHaD3j132C9NNKBrmHD4OMGOv_6GpWdOSHCpPHtWIXnhpA7WQo_ldHCeV2Nk9UBcaR-Jj4R4xvlng2AxF3ioFpjyg2q1UCI9wNZjbZmAFgVNPqe-q3Aucs9KWao_6sFjMrUkOdW';
    // payload data, it will vary according to requirement
    $data = [
        "registration_ids" => $firebaseToken,
        "notification" => [
            "title" => $title,
            "body" => $body,
            "photo" => $photo,
        ]
    ];
    $dataString = json_encode($data);

    $headers = [
        'Authorization: key=' . $SERVER_API_KEY,
        'Content-Type: application/json',
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

function saveNotification($user,$type , $title , $message,$teacher_id = null , $father_id = null , $student_id = null)
{
    \App\Models\Notification::create([
        'teacher_id'   => $teacher_id,
        'father_id'    => $father_id,
        'student_id'   => $student_id,
        'type'         => $type,
        'user'         => $user,
        'title'        => $title,
        'message'      => $message,
    ]);
}

function getStudentArrange($subjectId , $studentId , $points)
{
    return StudentRate::where('student_id' , '!=' , $studentId)
        ->whereSubjectId($subjectId)
        ->where('points' , '>' , $points)
        ->count();
}

//function sendMultiNotification($notificationTitle, $notificationBody, $devicesTokens , $type = null , $order_id = null)
//{
//
//    $optionBuilder = new OptionsBuilder();
//    $optionBuilder->setTimeToLive(60 * 20);
//
//    $notificationBuilder = new PayloadNotificationBuilder($notificationTitle);
//    $notificationBuilder->setBody($notificationBody)
//        ->setSound('default');
//
//    $dataBuilder = new PayloadDataBuilder();
//    $dataBuilder->addData(['type' => $type , 'order_id' => $order_id]);
//
//    $option = $optionBuilder->build();
//    $notification = $notificationBuilder->build();
//    $data = $dataBuilder->build();
//
//// You must change it to get your tokens
//    $tokens = $devicesTokens;
//
//    $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
//
//    $downstreamResponse->numberSuccess();
//    $downstreamResponse->numberFailure();
//    $downstreamResponse->numberModification();
//
////return Array - you must remove all this tokens in your database
//    $downstreamResponse->tokensToDelete();
//
////return Array (key : oldToken, value : new token - you must change the token in your database )
//    $downstreamResponse->tokensToModify();
//
////return Array - you should try to resend the message to the tokens in the array
//    $downstreamResponse->tokensToRetry();
//
//// return Array (key:token, value:errror) - in production you should remove from your database the tokens present in this array
//    $downstreamResponse->tokensWithError();
//
//    return ['success' => $downstreamResponse->numberSuccess(), 'fail' => $downstreamResponse->numberFailure()];
//}
//
//function saveNotification($userId, $title, $message, $type, $order_id = null)
//{
//    $created = \App\Notification::create([
//        'user_id' => $userId,
//        'title' => $title,
//        'type' => $type,
//        'message' => $message,
//        'order_id' => $order_id,
//    ]);
//    return $created;
//}

