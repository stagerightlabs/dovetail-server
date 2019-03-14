<?php

namespace App\Http\Requests;

use App\Category;
use Illuminate\Foundation\Http\FormRequest;

class CategoryUpdate extends FormRequest
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Determine if the user is authorized to make this request.
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
        $category = Category::findOrFail(hashid($this->route('hashid')));

        $this->offsetSet('category', $category);

        return [
            'name' => "required|iunique:categories,name,{$category->id},id,organization_id," . $this->organization()->id,
        ];
    }
}
