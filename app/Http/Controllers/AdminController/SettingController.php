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
    public function update_about(Request $request)
    {
        $about = AboutUs::firstOrfail();
        $this->validate($request , [
            'about_ar' => 'required',
            'about_en' => 'required',
        ]);
        $about->update($request->all());
        flash(trans('messages.updated'))->success();
        return redirect()->back();
    }
    public function contacts()
    {
        $contacts = ContactUs::orderBy('id' , 'desc')->get();
        return view('admin.contacts.index' , compact('contacts'));
    }
    public function reply($id , $reply)
    {
        $contactU = ContactUs::findOrFail($id);
        $contactU->update([
            'reply' => $reply,
        ]);
        flash(trans('messages.updated'))->success();
        return redirect()->back();
    }
    public function delete_contact($id)
    {
        $contact = ContactUs::findOrFail($id);
        $contact->delete();
        flash(trans('messages.deleted'))->success();
        return redirect()->back();
    }
    public function edit_contacts()
    {
        $contact = Contact::first();
        return view('admin.contacts.edit' , compact('contact'));
    }
    public function update_contacts(Request $request)
    {
        $this->validate($request , [
            'email'           => 'sometimes|email',
            'contact_number'  => 'sometimes',
            'twitter'         => 'sometimes',
            'facebook'        => 'sometimes',
            'instagram'       => 'sometimes',
            'linkedIn'        => 'sometimes',
            'google'          => 'sometimes',
            'address'         => 'sometimes',
            'site'            => 'sometimes',
            'message_ar'      => 'sometimes',
            'message_en'      => 'sometimes',
        ]);
        $contact = Contact::first();
        $contact->update([
            'email'          => $request->email == null ? $contact->email : $request->email,
            'contact_number' => $request->contact_number == null ? $contact->contact_number : $request->contact_number,
            'twitter'        => $request->twitter == null ? $contact->twitter : $request->twitter,
            'facebook'       => $request->facebook == null ? $contact->facebook : $request->facebook,
            'instagram'      => $request->instagram == null ? $contact->instagram : $request->instagram,
            'linkedIn'       => $request->linkedIn == null ? $contact->linkedIn : $request->linkedIn,
            'google'         => $request->google == null ? $contact->google : $request->google,
            'address'        => $request->address == null ? $contact->address : $request->address,
            'site'           => $request->site == null ? $contact->site : $request->site,
            'message_ar'     => $request->message_ar == null ? $contact->message_ar : $request->message_ar,
            'message_en'     => $request->message_en == null ? $contact->message_en : $request->message_en,
        ]);
        flash(trans('messages.updated'))->success();
        return redirect()->back();
    }
}
