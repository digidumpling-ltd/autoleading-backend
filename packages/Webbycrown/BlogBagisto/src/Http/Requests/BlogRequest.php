<?php

namespace Webbycrown\BlogBagisto\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Webbycrown\BlogBagisto\Validations\BlogUniqueSlug;

class BlogRequest extends FormRequest
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
        $locale = core()->getRequestedLocaleCode();

        if ($id = request()->route('id')) {
            return [
                "{$locale}.slug"              => ['required', new BlogUniqueSlug('blogs', $id)],
                "{$locale}.name"              => 'required',
                'src.*'                       => 'mimes:bmp,jpeg,jpg,png,webp',
                "{$locale}.short_description" => 'required',
                "{$locale}.description"       => 'required',
                'default_category'            => 'required',
                'tags.*'                      => 'required',
                'author_id'                   => 'required',
                "{$locale}.meta_title"        => 'required',
                "{$locale}.meta_description"  => 'required',
                "{$locale}.meta_keywords"     => 'required',
                'published_at'                => 'required',
            ];
        }

        return [
            'slug'              => ['required', new BlogUniqueSlug],
            'name'              => 'required',
            'src.*'             => 'mimes:bmp,jpeg,jpg,png,webp',
            'short_description' => 'required',
            'description'       => 'required',
            'meta_title'        => 'required',
            'meta_description'  => 'required',
            'meta_keywords'     => 'required',
            'default_category'  => 'required',
            'tags.*'            => 'required',
            'author_id'         => 'required',
            'published_at'      => 'required',
        ];
    }
}
