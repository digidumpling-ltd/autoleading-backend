<?php

namespace Webbycrown\BlogBagisto\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Webbycrown\BlogBagisto\Validations\BlogTagUniqueSlug;

class BlogTagRequest extends FormRequest
{
    /**
     * Determine if the Configuration is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $locale = core()->getRequestedLocaleCode() ?: 'en';

        if ($id = $this->id) {
            return [
                $locale.'.slug'             => ['required', new BlogTagUniqueSlug('blog_tags', $id)],
                $locale.'.name'             => 'required',
                $locale.'.description'      => 'required',
                'image.*'                   => 'mimes:bmp,jpeg,jpg,png,webp',
                $locale.'.meta_title'       => 'required',
                $locale.'.meta_description' => 'required',
                $locale.'.meta_keywords'    => 'required',
            ];
        }

        return [
            'slug'              => ['required', new BlogTagUniqueSlug],
            'name'              => 'required',
            'description'       => 'required',
            'image.*'           => 'mimes:bmp,jpeg,jpg,png,webp',
            'meta_title'        => 'required',
            'meta_description'  => 'required',
            'meta_keywords'     => 'required',
        ];
    }
}
