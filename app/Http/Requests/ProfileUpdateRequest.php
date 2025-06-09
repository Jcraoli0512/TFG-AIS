<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'biography' => ['nullable', 'string', 'max:1000'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            
            // Redes sociales
            'instagram' => ['nullable', 'url', 'max:255'],
            'twitter' => ['nullable', 'url', 'max:255'],
            'tiktok' => ['nullable', 'url', 'max:255'],
            'youtube' => ['nullable', 'url', 'max:255'],
            'pinterest' => ['nullable', 'url', 'max:255'],
            'linkedin' => ['nullable', 'url', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'profile_photo.image' => 'El archivo debe ser una imagen.',
            'profile_photo.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif.',
            'profile_photo.max' => 'La imagen no debe pesar más de 2MB.',
            
            // Mensajes para redes sociales
            'instagram.url' => 'El enlace de Instagram debe ser una URL válida.',
            'twitter.url' => 'El enlace de Twitter debe ser una URL válida.',
            'tiktok.url' => 'El enlace de TikTok debe ser una URL válida.',
            'youtube.url' => 'El enlace de YouTube debe ser una URL válida.',
            'pinterest.url' => 'El enlace de Pinterest debe ser una URL válida.',
            'linkedin.url' => 'El enlace de LinkedIn debe ser una URL válida.',
        ];
    }
}
