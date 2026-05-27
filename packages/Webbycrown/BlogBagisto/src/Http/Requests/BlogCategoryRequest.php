<?php

namespace Webbycrown\BlogBagisto\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Webbycrown\BlogBagisto\Validations\BlogCategoryUniqueSlug;

class BlogCategoryRequest extends FormRequest
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
                $locale.'.slug'             => ['required', new BlogCategoryUniqueSlug('blog_categories', $id)],
                $locale.'.name'             => 'required',
                'image.*'                   => 'mimes:bmp,jpeg,jpg,png,webp',
                $locale.'.description'      => 'required',
                $locale.'.meta_title'       => 'required',
                $locale.'.meta_description' => 'required',
                $locale.'.meta_keywords'    => 'required',
            ];
        }

        return [
            'slug'              => ['required', new BlogCategoryUniqueSlug],
            'name'              => 'required',
            'image.*'           => 'mimes:bmp,jpeg,jpg,png,webp',
            'description'       => 'required',
            'meta_title'        => 'required',
            'meta_description'  => 'required',
            'meta_keywords'     => 'required',
        ];
    }
}
