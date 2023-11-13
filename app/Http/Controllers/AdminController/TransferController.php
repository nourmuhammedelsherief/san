<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\School\SchoolSubscription;
use App\Models\Teacher\Teacher;
use App\Models\Teacher\TeacherSubscription;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    public function teacher_transfers()
    {
        $transfers = TeacherSubscription::wherePaymentType('bank')
            ->wherePayment('false')
            ->where('transfer_photo' , '!=' , null)
            ->where('status' , 'not_active')
            ->get();
        return view('admin.settings.teacher_transfers' , compact('transfers'));
    }
    public function teacher_transfer_submit($id , $status)
    {
        $subscription = TeacherSubscription::findOrFail($id);
        if ($status == 'done')
        {
            $subscription->update([
                'status'  => 'active',
                'payment' => 'true',
                'paid_at' => Carbon::now(),
                'end_at'  => Carbon::now()->addYear(),
            ]);
            $subscription->teacher->update([
                'active'  => 'true',
            ]);
            if ($subscription->invitation_code_id != null)
            {
                $subscription->invitation_code->update([
                    'balance' => $subscription->invitation_code->balance + $subscription->invitation_discount
                ]);
            }
            // add operation to History
            History::create([
                'teacher_id'  => $subscription->teacher->id,
                'amount'      => $subscription->paid_amount,
                'discount'    => $subscription->discount,
                'type'        => 'teacher',
                'transfer_photo' => $subscription->transfer_photo,
                'payment_type' => 'bank'
            ]);
            flash(trans('messages.payment_done_successfully'))->success();
            return redirect()->back();
        }elseif ($status == 'remove')
        {
            $subscription->update([
                'payment_type' => null,
                'transfer_photo' => null,
            ]);
            flash(trans('messages.paymentCanceledSuccessfully'))->success();
            return redirect()->back();
        }
    }

    public function schools_transfers()
    {
        $transfers = SchoolSubscription::wherePaymentType('bank')
            ->wherePayment('false')
            ->where('transfer_photo' , '!=' , null)
            ->where('status' , 'not_active')
            ->get();
        return view('admin.settings.school_transfers' , compact('transfers'));
    }
    public function schools_transfers_submit($id , $status)
    {
        $subscription = SchoolSubscription::findOrFail($id);
        if ($status == 'done')
        {
            $subscription->update([
                'status'  => 'active',
                'payment' => 'true',
                'paid_at' => Carbon::now(),
                'end_at'  => Carbon::now()->addYear(),
            ]);
            $subscription->school->update([
                'status'  => 'active',
            ]);
            // Active the teachers belongs to school
            $teachers = Teacher::with('teacher_classrooms')
                ->whereHas('teacher_classrooms', function ($q) use ($subscription){
                    $q->with('classroom');
                    $q->whereHas('classroom', function ($c) use ($subscription){
                        $c->whereSchoolId($subscription->school->id);
                    });
                })->get();
            if ($teachers->count() > 0)
            {
                foreach ($teachers as $teacher)
                {
                    $teacher->update([
                        'active' => 'true'
                    ]);
                }
            }
            // add operation to History
            History::create([
                'school_id'  => $subscription->school->id,
                'amount'      => $subscription->paid_amount,
                'discount'    => $subscription->discount,
                'type'        => 'school',
                'transfer_photo' => $subscription->transfer_photo,
                'payment_type' => 'bank'
            ]);
            flash(trans('messages.payment_done_successfully'))->success();
            return redirect()->back();
        }elseif ($status == 'remove')
        {
            $subscription->update([
                'payment_type' => null,
                'transfer_photo' => null,
            ]);
            flash(trans('messages.paymentCanceledSuccessfully'))->success();
            return redirect()->back();
        }
    }

}
