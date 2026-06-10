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
            'work_start' => 'required|date_format:H:i',
            'work_end' => 'required|date_format:H:i',
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

            // ① 出勤・退勤が未入力なら休憩のバリデーションチェックはしない
            if (!$workStart || !$workEnd){
                return;
            }

            // ② 出勤 > 退勤
            if ($workStart && $workEnd && $workStart > $workEnd) {
                $validator->errors()->add('work_start', '出勤時間もしくは退勤時間が不適切な値です');
                return; // 休憩チェックはしない
            }

            // ③ 休憩開始の範囲チェック
            foreach ($starts as $i => $start) {
                if ($start !== null && $start !== '') {
                    if ($start < $workStart || $start > $workEnd)    
                    {
                        $validator->errors()->add("break_start.$i", '休憩時間が不適切な値です');
                    }
                }
            }

            // ④ 休憩終了の範囲チェック
            foreach ($ends as $i => $end) {
                $start = $starts[$i] ?? null;

                if ($start !== null && $end !== '') {
                    // 休憩終了 <　休憩開始
                    if ($start !== null && $start !== '' && $end < $start) {
                        $validator->errors()->add("break_end.$i", '休憩時間もしくは退勤時間が不適切な値です');
                    }

                    // 退勤より後
                    if ($end > $workEnd) {
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
