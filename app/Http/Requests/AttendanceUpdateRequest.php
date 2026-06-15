<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'attendance_id' => 'required|exists:attendances,id',
            'work_start' => 'nullable|date_format:H:i',
            'work_end' => 'nullable|date_format:H:i',
            'break_start.*' => 'nullable|date_format:H:i' ,
            'break_end.*' => 'nullable|date_format:H:i',
            'note' => 'required|string|max:255'
        ];
    }

     public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $workStart = $this->work_start;
            $workEnd   = $this->work_end;

            $starts = $this->break_start ?? [];
            $ends   = $this->break_end ?? [];

        
            // 出勤 > 退勤
            if ($workStart && $workEnd && $workStart > $workEnd) {
                $validator->errors()->add('work_start', '出勤時間もしくは退勤時間が不適切な値です');
                return; // 休憩チェックはしない
            }

            // 休憩開始の範囲チェック
            foreach ($starts as $i => $start) {
                $end = $ends[$i] ?? null;

                if ($start || $end){
                    if (!$workStart || !$workEnd || $start < $workStart || $start > $workEnd)    
                    {
                            $validator->errors()->add("break_start.$i", '休憩時間が不適切な値です');
                    }
                
                    // 休憩終了 <　休憩開始  休憩終了 > 退勤
                        if (!$workStart || !$workEnd ||  
                            ($start &&  $end < $start) || $end > $workEnd) {
                        $validator->errors()->add("break_end.$i", '休憩時間もしくは退勤時間が不適切な値です');
                    }

                }
            
            }
        });
    }

     public function messages()
    {
        return [
            'note.required' => '備考を記入してください'
        ];
    }
}
