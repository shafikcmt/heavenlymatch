<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UploadPhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'photo' => [
                'required',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',   // 4 MB
                'dimensions:min_width=200,min_height=200,max_width=8000,max_height=8000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'photo.required'   => __('validation.photo_required'),
            'photo.image'      => __('validation.photo_image'),
            'photo.mimes'      => __('validation.photo_mimes'),
            'photo.max'        => __('validation.photo_max_size'),
            'photo.dimensions' => __('validation.photo_dimensions'),
        ];
    }
}
