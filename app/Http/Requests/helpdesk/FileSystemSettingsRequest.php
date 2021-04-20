<?php

namespace App\Http\Requests\helpdesk;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class FileSystemSettingsRequest extends Request
{
    use RequestJsonValidation;

    public function rules()
    {
        return [
            'allowedFiles' => [
                'required',
                'regex:/^[a-zA-Z0-9]+(,[a-zA-Z0-9]+)*$/',
                function ($attribute, $value, $fail) {
                    $scriptFiles = array_intersect(explode(',', $value), ['php','js','xml','sql','cpp','c','cs','py','java']);
                    if ($scriptFiles) {
                        $fail(implode(',', $scriptFiles) . " files are not allowed for security concerns.");
                    }
                }
                ],
            'showPublicDiskWithDefaultDisk' => 'required|integer|in:0,1',
        ];
    }

    /**
     * @inheritDoc
     */
    public function messages()
    {
        return [
            '*.required'  => trans('filemanager::lang.file-manager-required-fields'),
            '*.in'  => trans('filemanager::lang.file-manager-invalid-fields'),
            'allowedFiles.regex' => trans('filemanager::lang.file-manager-invalid-csv'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function prepareForValidation()
    {
        $this->merge(['allowedFiles' => strtolower($this->allowedFiles)]);
    }
}
