<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplicationRequest extends FormRequest
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
            'corrected_work_start' => 'nullable|date_format:H:i',
            'corrected_work_end' => 'nullable|date_format:H:i',
            'corrected_break_start.*' => 'nullable|date_format:H:i' ,
            'corrected_break_end.*' => 'nullable|date_format:H:i',
            'reason' => 'required|string|max:255'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $workStart = $this->corrected_work_start;
            $workEnd   = $this->corrected_work_end;

            $starts = $this->corrected_break_start ?? [];
            $ends   = $this->corrected_break_end ?? [];

            // 休憩開始の範囲チェック
            foreach ($starts as $i => $start) {

                $end = $ends[$i] ?? null;

                // 休憩が入力されているときだけ
                if ($start || $end){
                    if (!$workStart || !$workEnd || $start < 
                        $workStart || $start > $workEnd) {
                        $validator->errors()->add("corrected_break_start.$i", '休憩時間が不適切な値です');
                    }

                    // 休憩終了 < 休憩開始  休憩終了 > 退勤
                    if (!$workStart || !$workEnd || ($start &&   
                        $end < $start) || $end > $workEnd) {
                        $validator->errors()->add("corrected_break_end.$i", '休憩時間もしくは退勤時間が不適切な値です');
                    }
                }
            }
        });
    }

     public function messages()
    {
        return [
            'reason.required' => '備考を記入してください'
        ];
    }
}
