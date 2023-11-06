<?php

namespace App\Http\Resources\Teacher;

use App\Http\Resources\Student\StudentRateResource;
use App\Http\Resources\Student\StudentRewardResource;
use App\Models\Teacher\StudentReward;
use App\Models\Teacher\TeacherSubject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $default_subject = TeacherSubject::whereTeacherId($request->user()->id)->first();
        if ($this->student)
        {
            return [
                'id'    => $this->student->id,
                'classroom_id'  => $this->student->classroom_id,
                'classroom'    => $this->student->classroom->name,
                'name'     => $this->student->name,
                'gender'   => $this->student->gender,
                'photo'    => $this->student->photo == null ? null : asset('/uploads/students/' . $this->student->photo),
                'birth_date' => $this->student->birth_date->format('Y-m-d'),
                'age'      => \Carbon\Carbon::parse($this->student->birth_date)->diff(\Carbon\Carbon::now())->format('%y'),
                'points'  => $request->subject_id != null ? intval($this->student->rates()->whereSubjectId($request->subject_id)->sum('points')) : ($default_subject ? intval($this->student->rates()->whereSubjectId($default_subject->subject_id)->sum('points')) : intval($this->student->rates()->sum('points'))),
                'rates'   => $request->subject_id != null ? StudentRateResource::collection($this->student->rates()->whereSubjectId($request->subject_id)->get()) : ($default_subject ? StudentRateResource::collection($this->student->rates()->whereSubjectId($default_subject->subject_id)->get()) : StudentRateResource::collection($this->student->rates()->get())),
                'rewards' => $request->subject_id != null ? StudentRewardResource::collection($this->student->rewards()->whereSubjectId($request->subject_id)->get()) : ($default_subject ? StudentRewardResource::collection($this->student->rewards()->whereSubjectId($default_subject->subject_id)->get()) : StudentRewardResource::collection($this->student->rewards()->get())),
                'reward_points'  => $request->subject_id != null ? intval($this->student->rewards()->whereSubjectId($request->subject_id)->sum('points')) : ($default_subject ? intval($this->student->rewards()->whereSubjectId($default_subject->subject_id)->sum('points')) : intval($this->student->rewards()->sum('points'))),
                'identity_id' => $this->student->identity_id,
                'password'  => $this->student->un_hashed_password,
                'api_token'  => $this->student->api_token,
            ];
        }else{
            return [
                'id'    => $this->id,
                'classroom_id'  => $this->classroom_id,
                'classroom'    => $this->classroom->name,
                'name'     => $this->name,
                'gender'   => $this->gender,
                'photo'    => $this->photo == null ? null : asset('/uploads/students/' . $this->photo),
                'birth_date' => $this->birth_date->format('Y-m-d'),
                'age'      => \Carbon\Carbon::parse($this->birth_date)->diff(\Carbon\Carbon::now())->format('%y'),
                'points'  => $request->subject_id != null ? intval($this->rates()->whereSubjectId($request->subject_id)->sum('points')) : ($default_subject ? intval($this->rates()->whereSubjectId($default_subject->subject_id)->sum('points')) : intval($this->rates()->sum('points'))),
                'rates'   => $request->subject_id != null ? StudentRateResource::collection($this->rates()->whereSubjectId($request->subject_id)->get()) : ($default_subject ? StudentRateResource::collection($this->rates()->whereSubjectId($default_subject->subject_id)->get()) : StudentRateResource::collection($this->rates()->get())),
                'rewards' => $request->subject_id != null ? StudentRewardResource::collection($this->rewards()->whereSubjectId($request->subject_id)->get()) : ($default_subject ? StudentRewardResource::collection($this->rewards()->whereSubjectId($default_subject->subject_id)->get()) : StudentRewardResource::collection($this->rewards()->get())),
                'reward_points'  => $request->subject_id != null ? intval($this->rewards()->whereSubjectId($request->subject_id)->sum('points')) : ($default_subject ? intval($this->rewards()->whereSubjectId($default_subject->subject_id)->sum('points')) : intval($this->rewards()->sum('points'))),
                'identity_id' => $this->identity_id,
                'password'  => $this->un_hashed_password,
                'api_token'  => $this->api_token,
            ];
        }
    }
}
