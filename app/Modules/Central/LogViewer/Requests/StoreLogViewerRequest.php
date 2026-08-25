<?php

namespace App\Modules\Central\LogViewer\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLogViewerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
}
