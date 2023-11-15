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

function sendNotification($firebaseToken, $title, $body, $photo = null)
{
    $SERVER_API_KEY = 'AAAA6XeJCl8:APA91bF-kul3qdiL-CwY2n8_wv0upJNC_hjHT7A3N8b1EkzdoGze73pR1OhIhf9ufVdOdRa0tY_FgSwNqd94RDZHDaNKrDIxszzCV_RtbQ_lN7NbqhwFDAdbgkTGALdg2-54kmM97x5w';
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

function saveNotification($user, $type, $title, $message, $teacher_id = null, $father_id = null, $student_id = null, $photo = null)
{
    \App\Models\Notification::create([
        'teacher_id' => $teacher_id,
        'father_id' => $father_id,
        'student_id' => $student_id,
        'type' => $type,
        'user' => $user,
        'title' => $title,
        'message' => $message,
        'photo' => $photo,
    ]);
}

function getStudentArrange($subjectId, $studentId, $points)
{
    $student = \App\Models\Student::find($studentId);
    $classStudents = \App\Models\Student::whereClassroomId($student->classroom->id)
        ->where('id', '!=', $studentId)
        ->get();
    $count = 0;
    foreach ($classStudents as $classStudent) {
        $rate = StudentRate::whereStudentId($classStudent->id)->whereSubjectId($subjectId)->sum('points');
        if ($rate > $points) {
            $count++;
        }
    }
    return $count;
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

function tamara()
{
    $basURL = "https://api-sandbox.tamara.co/checkout/payment-options-pre-check";

    $body = array(
        "country" => "SA",
        "order_value" => array(
            "amount" => "300.00",
            "currency" => "SAR"
        ),
        "phone_number" => "966503334444",
        "is_vip" => true
    );
    $headers = array(
        'Content-type: application/json',
        'Accept: application/json',
        'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhY2NvdW50SWQiOiIyZmIyOWVjZC0yNWExLTQ5NTgtOTc5Yi0zZThkNTVlNzQwMmIiLCJ0eXBlIjoibWVyY2hhbnQiLCJzYWx0IjoiOWVlZDA1YmM0YTkxMDUzOWYyMjQ3NzU4NjkwNzZmMzMiLCJyb2xlcyI6WyJST0xFX01FUkNIQU5UIl0sImlhdCI6MTY5OTQ0OTMwMiwiaXNzIjoiVGFtYXJhIn0.dKtVudFsEcbOC1tOLRtGekgWB1VwFtnUaTDb5UfPGNaXFO91hcN7SW0nk98qaz3ybOE8IMYTIXSG2zJB7wWxhMdPDVKczre0wQzdngP24Ufzu5siZ-AQLuUvEB8Xi1v16T25hukVo-sMBmE2sIpReEl5XxNkJw5UHpCsGDhi5WuIFGruv7hFlCR9ZPNc7smMbNM0KfvBBJpmItIUlU_ZqVHh5loD07XY6lFd4l6XrhVP-AjQ1uK0TJ_3cKNFXPUPaPmmHFQkSroS6YsxHrCaoxoeOzdJtS8PDM3pALg_oT7-g_xutJlDeSX5cqszAUfwJpESr5ts0OpUvDza2h4JZg'
    );
    $body = json_encode($body);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $basURL,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_HTTPHEADER => $headers,

    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        return $err;
    } else {
        $response = array_values(json_decode($response, true));
        dd($response);
        return $response[0]['id_token'];
    }
}

function tamara_checkOut()
{
    $basURL = "https://api-sandbox.tamara.co/checkout";

    $body = array(
        "total_amount" => array(
            "amount" => 300,
            "currency" => "SAR"
        ),
        "shipping_amount" => array(
            "amount" => 0,
            "currency" => "SAR"
        ),
        "tax_amount" => array(
            "amount" => 0,
            "currency" => "SAR"
        ),
        "order_reference_id" => "12312348883-abda-fdfe--afd31241",
        "order_number" => "S12856",
        "discount" => array(
            "amount" => array(
                "amount" => 200,
                "currency" => "SAR"
            )
        ,
            "name" => "Christmas 2020"
        ),
        "items" => array(
            array(
                "name" => "Lego City 8601",
                "type" => "Digital",
                "reference_id" => "123",
                "sku" => "SA-12436",
                "quantity" => 1,
                "discount_amount" => array(
                    "amount" => 100,
                    "currency" => "SAR"
                ),
                "tax_amount" => array(
                    "amount" => 10,
                    "currency" => "SAR"
                ),
                "unit_price" => array(
                    "amount" => 490,
                    "currency" => "SAR"
                ),
                "total_amount" => array(
                    "amount" => 100,
                    "currency" => "SAR"
                )
            )
        ),
        "consumer" => array(
            "email" => "customer@email.com",
            "first_name" => "Mona",
            "last_name" => "Lisa",
            "phone_number" => "580491109"
        ),
        "country_code" => "SA",
        "description" => "lorem ipsum dolor",
        "merchant_url" => array(
            "cancel" => "https://dashboard.takia-app.com/takia_webhook",
            "failure" => "https://dashboard.takia-app.com/takia_webhook",
            "success" => "https://dashboard.takia-app.com/takia_webhook",
            "notification" => "https://dashboard.takia-app.com/takia_webhook"
        ),
        "payment_type" => "PAY_BY_INSTALMENTS",
        "instalments" => 1,
        "billing_address" => array(
            "city" => "Riyadh",
            "country_code" => "SA",
            "first_name" => "Mona",
            "last_name" => "Lisa",
            "line1" => "3764 Al Urubah Rd",
            "line2" => "string",
            "phone_number" => "532298658",
            "region" => "As Sulimaniyah"
        ),
        "shipping_address" => array(
            "city" => "Riyadh",
            "country_code" => "SA",
            "first_name" => "Mona",
            "last_name" => "Lisa",
            "line1" => "3764 Al Urubah Rd",
            "line2" => "string",
            "phone_number" => "532298658",
            "region" => "As Sulimaniyah"
        ),
        "platform" => "platform name here",
        "is_mobile" => false,
        "locale" => "en_US",
        "risk_assessment" => array(
            "customer_age" => 22,
            "customer_dob" => "31-01-2000",
            "customer_gender" => "Male",
            "customer_nationality" => "SA",
            "is_premium_customer" => true,
            "is_existing_customer" => true,
            "is_guest_user" => true,
            "account_creation_date" => "31-01-2019",
            "platform_account_creation_date" => "string",
            "date_of_first_transaction" => "31-01-2019",
            "is_card_on_file" => true,
            "is_COD_customer" => true,
            "has_delivered_order" => true,
            "is_phone_verified" => true,
            "is_fraudulent_customer" => true,
            "total_ltv" => 501.5,
            "total_order_count" => 12,
            "order_amount_last3months" => 301.5,
            "order_count_last3months" => 2,
            "last_order_date" => "31-01-2021",
            "last_order_amount" => 301.5,
            "reward_program_enrolled" => true,
            "reward_program_points" => 300,
            "phone_verified" => false
        ),
        "additional_data" => array(
            "delivery_method" => "home delivery",
            "pickup_store" => "Store A",
            "store_code" => "Store code A",
            "vendor_amount" => 0,
            "merchant_settlement_amount" => 0,
            "vendor_reference_code" => "AZ1234"
        )
    );
    $headers = array(
        'Content-type: application/json',
        'Accept: application/json',
        'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhY2NvdW50SWQiOiIyZmIyOWVjZC0yNWExLTQ5NTgtOTc5Yi0zZThkNTVlNzQwMmIiLCJ0eXBlIjoibWVyY2hhbnQiLCJzYWx0IjoiOWVlZDA1YmM0YTkxMDUzOWYyMjQ3NzU4NjkwNzZmMzMiLCJyb2xlcyI6WyJST0xFX01FUkNIQU5UIl0sImlhdCI6MTY5OTQ0OTMwMiwiaXNzIjoiVGFtYXJhIn0.dKtVudFsEcbOC1tOLRtGekgWB1VwFtnUaTDb5UfPGNaXFO91hcN7SW0nk98qaz3ybOE8IMYTIXSG2zJB7wWxhMdPDVKczre0wQzdngP24Ufzu5siZ-AQLuUvEB8Xi1v16T25hukVo-sMBmE2sIpReEl5XxNkJw5UHpCsGDhi5WuIFGruv7hFlCR9ZPNc7smMbNM0KfvBBJpmItIUlU_ZqVHh5loD07XY6lFd4l6XrhVP-AjQ1uK0TJ_3cKNFXPUPaPmmHFQkSroS6YsxHrCaoxoeOzdJtS8PDM3pALg_oT7-g_xutJlDeSX5cqszAUfwJpESr5ts0OpUvDza2h4JZg'
    );
    $body = json_encode($body);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $basURL,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_HTTPHEADER => $headers,

    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        return $err;
    } else {
        //Save the order_id and checkout_id in your DBs
        $response = array_values(json_decode($response, true));
        return $response[2];
    }
}

function tamara_capture()
{
    $basURL = "https://api-sandbox.tamara.co/payments/capture";
    $body = array(
        "order_id" => "28388313-a324-4733-a2a5-c97cb9dec60a",
        "total_amount" => array(
            "amount" => 1400,
            "currency" => "SAR"
        ),
        "items" => array(
            array(
                "name" => "nabil",
                "type" => "Digital",
                "reference_id" => "328",
                "sku" => "328",
                "quantity" => 1,
                "discount_amount" => array(
                    "amount" => 0,
                    "currency" => "SAR"
                ),
                "tax_amount" => array(
                    "amount" => 0,
                    "currency" => "SAR"
                ),
                "unit_price" => array(
                    "amount" => 1400,
                    "currency" => "SAR"
                ),
                "total_amount" => array(
                    "amount" => 1400,
                    "currency" => "SAR"
                )
            ),
        ),
        "discount_amount" => array(
            "amount" => 0,
            "currency" => "SAR"
        ),
        "shipping_amount" => array(
            "amount" => 0,
            "currency" => "SAR"
        ),
        "shipping_info" => array(
            "shipped_at" => "2020-03-31T19:19:52.677Z",
            "shipping_company" => "DHL",
            "tracking_number" => 100,
            "tracking_url" => "https://shipping.com/tracking?id=123456"
        ),
        "tax_amount" => array(
            "amount" => 0,
            "currency" => "SAR"
        )
    );

    $headers = array(
        'Content-type: application/json',
        'Accept: application/json',
        'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhY2NvdW50SWQiOiIyZmIyOWVjZC0yNWExLTQ5NTgtOTc5Yi0zZThkNTVlNzQwMmIiLCJ0eXBlIjoibWVyY2hhbnQiLCJzYWx0IjoiOWVlZDA1YmM0YTkxMDUzOWYyMjQ3NzU4NjkwNzZmMzMiLCJyb2xlcyI6WyJST0xFX01FUkNIQU5UIl0sImlhdCI6MTY5OTQ0OTMwMiwiaXNzIjoiVGFtYXJhIn0.dKtVudFsEcbOC1tOLRtGekgWB1VwFtnUaTDb5UfPGNaXFO91hcN7SW0nk98qaz3ybOE8IMYTIXSG2zJB7wWxhMdPDVKczre0wQzdngP24Ufzu5siZ-AQLuUvEB8Xi1v16T25hukVo-sMBmE2sIpReEl5XxNkJw5UHpCsGDhi5WuIFGruv7hFlCR9ZPNc7smMbNM0KfvBBJpmItIUlU_ZqVHh5loD07XY6lFd4l6XrhVP-AjQ1uK0TJ_3cKNFXPUPaPmmHFQkSroS6YsxHrCaoxoeOzdJtS8PDM3pALg_oT7-g_xutJlDeSX5cqszAUfwJpESr5ts0OpUvDza2h4JZg'
    );
    $body = json_encode($body);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $basURL,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_HTTPHEADER => $headers,

    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        return $err;
    } else {
        dd($response);
        //Save the order_id and checkout_id in your DBs
        $response = array_values(json_decode($response, true));
        dd($response);
        return $response[2];
    }
}

function order_authorise($order_id)
{
    $basURL = 'https://api-sandbox.tamara.co/orders/' . $order_id . '/authorise';

    $headers = array(
        'Content-type: application/json',
        'Accept: application/json',
        'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhY2NvdW50SWQiOiIyZmIyOWVjZC0yNWExLTQ5NTgtOTc5Yi0zZThkNTVlNzQwMmIiLCJ0eXBlIjoibWVyY2hhbnQiLCJzYWx0IjoiOWVlZDA1YmM0YTkxMDUzOWYyMjQ3NzU4NjkwNzZmMzMiLCJyb2xlcyI6WyJST0xFX01FUkNIQU5UIl0sImlhdCI6MTY5OTQ0OTMwMiwiaXNzIjoiVGFtYXJhIn0.dKtVudFsEcbOC1tOLRtGekgWB1VwFtnUaTDb5UfPGNaXFO91hcN7SW0nk98qaz3ybOE8IMYTIXSG2zJB7wWxhMdPDVKczre0wQzdngP24Ufzu5siZ-AQLuUvEB8Xi1v16T25hukVo-sMBmE2sIpReEl5XxNkJw5UHpCsGDhi5WuIFGruv7hFlCR9ZPNc7smMbNM0KfvBBJpmItIUlU_ZqVHh5loD07XY6lFd4l6XrhVP-AjQ1uK0TJ_3cKNFXPUPaPmmHFQkSroS6YsxHrCaoxoeOzdJtS8PDM3pALg_oT7-g_xutJlDeSX5cqszAUfwJpESr5ts0OpUvDza2h4JZg'
    );
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $basURL,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_HTTPHEADER => $headers,

    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        return $err;
    } else {
        //Save the order_id and checkout_id in your DBs
        $response = array_values(json_decode($response, true));
        return 0;
    }
}
