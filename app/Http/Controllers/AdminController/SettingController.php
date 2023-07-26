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
        ]);
        $setting = Setting::find(1);
        $setting->update($request->all());
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
    public function histories()
    {
        $histories = History::orderBy('id' , 'desc')
            ->orderBy('id' , 'desc')
            ->paginate(200);
        return view('admin.settings.histories' , compact('histories'));
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
}
