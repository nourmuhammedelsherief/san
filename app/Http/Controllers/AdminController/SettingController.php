<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use App\Models\Contact;
use App\Models\ContactUs;
use App\Models\History;
use App\Models\Setting;
use App\Models\UserCourse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function setting()
    {
        $setting = Setting::find(1);
        return view('admin.settings.setting' , compact('setting'));
    }

    public function store_setting(Request $request)
    {
        $this->validate($request , [
            'bank_name'       => 'sometimes',
            'account_number'  => 'sometimes',
            'Iban_number'     => 'sometimes',
            'online_token'    => 'sometimes',
            'bearer_token'    => 'sometimes',
            'sender_name'     => 'sometimes',
            'logo'            => 'sometimes|mimes:jpg,jpeg,png,gif,tif,psd,pmp|max:5000',
            'contact_number'  => 'sometimes',
            'payment_type'    => 'sometimes',
            'site_url'        => 'sometimes'
        ]);
        $setting = Setting::find(1);
        $setting->update([
            'bank_name'       => $request->bank_name,
            'account_number' => $request->account_number,
            'Iban_number' => $request->Iban_number,
            'online_token' => $request->online_token,
            'bearer_token' => $request->bearer_token,
            'sender_name' => $request->sender_name,
            'school_subscribe_price' => $request->school_subscribe_price,
            'teacher_subscribe_price' => $request->teacher_subscribe_price,
            'invitation_code_discount' => $request->invitation_code_discount,
            'payment_type' => $request->payment_type,
            'contact_number' => $request->contact_number,
            'site_url' => $request->site_url,
            'logo' => $request->file('logo') == null ? $setting->logo : UploadImageEdit($request->file('logo') , 'logo' , '/uploads' , $setting->logo)
        ]);
        flash(trans('messages.updated'))->success();
        return redirect()->back();
    }
    public function bank_transfers()
    {
        $transfers = UserCourse::whereStatus('not_active')
            ->wherePayment('false')
            ->where('transfer_photo' , '!=' , null)
            ->orderBy('id' , 'desc')
            ->get();
        return view('admin.settings.transfers' , compact('transfers'));
    }
    public function bank_transfer_submit($id, $status)
    {
        $course = UserCourse::findOrFail($id);
        if ($status == 'done')
        {
            if ($course->course->type == 'recorded'):
                $end_at = Carbon::now()->addDays($course->course->period);
            else:
                $end_at = $course->course->start_at->addDays($course->course->period);
            endif;
            $course->update([
                'status'  => 'active',
                'payment' => 'true',
                'end_at'  => $end_at
            ]);
            // record operation at history
            History::create([
                'user_id'    => $course->user_id,
                'course_id'  => $course->course_id,
                'transfer_photo' => $course->transfer_photo,
                'price'  => $course->price,
            ]);
            flash(trans('messages.confirmed_successfully'))->success();
        }elseif ($status == 'remove')
        {
            if ($course->tranfer_photo != null)
            {
                @unlink(public_path('/uploads/transfers/' . $course->transfer_photo));
            }
            $course->delete();
            flash(trans('messages.deleted'))->success();
        }
        return redirect()->back();
    }
    public function histories($type = null)
    {
        if ($type):
            $histories = History::whereType($type)
                ->orderBy('id' , 'desc')
                ->paginate(200);
        else:
            $histories = History::orderBy('id' , 'desc')
                ->paginate(200);
        endif;

        return view('admin.settings.histories' , compact('histories' , 'type'));
    }
    public function delete_history($id)
    {
        $history = History::find($id);
        $history->delete();
        flash(trans('messages.deleted'))->success();
        return redirect()->back();
    }
    public function about()
    {
        $about = AboutUs::firstOrfail();
        return view('admin.settings.about' , compact('about'));
    }
    public function update_about(Request $request)
    {
        $about = AboutUs::firstOrfail();
        $this->validate($request , [
            'about_ar' => 'sometimes',
            'about_en' => 'sometimes',
        ]);
        $about->update($request->all());
        flash(trans('messages.updated'))->success();
        return redirect()->back();
    }
}
