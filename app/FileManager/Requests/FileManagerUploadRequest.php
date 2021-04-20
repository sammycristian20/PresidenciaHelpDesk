<?php

namespace App\FileManager\Requests;

use App\FileManager\Services\ConfigService\ConfigRepository;
use App\Model\helpdesk\Settings\FileSystemSettings;
use App\Traits\RequestJsonValidation;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Storage;

class FileManagerUploadRequest extends FormRequest
{
    public function rules()
    {
        $config = resolve(ConfigRepository::class);

        $fileValidationRules = $this->getFileValidationRules();

        return [
            'disk' => [
                'sometimes',
                'string',
                function ($attribute, $value, $fail) use ($config) {
                    if (
                        !in_array($value, $config->getDiskList()) ||
                        !array_key_exists($value, config('filesystems.disks'))
                    ) {
                        return $fail('diskNotFound');
                    }
                },
            ],
            'path' => [
                'sometimes',
                'string',
                'nullable',
                function ($attribute, $value, $fail) {
                    if (
                        $value && !Storage::disk($this->input('disk'))->exists($value)
                    ) {
                        return $fail('pathNotFound');
                    }
                },
            ],
            'files' => [
                'sometimes',
                'array',
                'nullable',
                'max:' . $fileValidationRules['maxNumberOfFiles']
            ],
            'files.*' => [
                'sometimes',
                'nullable',
                'file',
                'max:' . $fileValidationRules['maxUploadSize'],
                function ($attribute, $value, $fail) use ($fileValidationRules) {
                    $allowedFiles = FileSystemSettings::value('allowed_files');
                    if (! in_array($value->getClientOriginalExtension(), explode(',', $allowedFiles))) {
                        return $fail(trans(
                            'filemanager::lang.file-manager-invalid-mime-error',
                            ['files' => implode(',', $fileValidationRules['allowedMimes'])]
                        ));
                    }
                }
            ]
        ];
    }

    /**
     * @inheritDoc
     *
     * @return array
     */
    public function messages()
    {
        $fileValidationRules = $this->getFileValidationRules();

        return [
            'files.*.mimes' => trans(
                'filemanager::lang.file-manager-invalid-mime-error',
                ['files' => implode(',', $fileValidationRules['allowedMimes'])]
            ),
            'files.*.size' => trans(
                'filemanager::lang.file-manager-invalid-file-size',
                ['size' => ($fileValidationRules['maxUploadSize'] / 1024)]
            ),
        ];
    }

    /**
     * Gets the file validation rules for uploads
     *
     * @return array
     */
    private function getFileValidationRules()
    {
        return [
            'maxNumberOfFiles' => ini_get('max_file_uploads'),
            'allowedMimes' => $this->getAllowedMimes() ?: config('filesystems.allowed_mime_types_public'),
            'maxUploadSize' => ((int) ini_get('post_max_size')) * 1024
        ];
    }

    /**
     *Returns allowed File MimeTypes
     *
     * @return array|false|string[]
     */
    private function getAllowedMimes()
    {
        $fileSystemSettings = new FileSystemSettings();

        return $fileSystemSettings->count()
            ? explode(',', $fileSystemSettings->value('allowed_files'))
            : [];
    }

    /**
     * @inheritDoc
     *
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->messages();

        $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($errors));

        $messageArray = iterator_to_array($iterator, false);

        throw new HttpResponseException(errorResponse(reset($messageArray), 412));
    }
}
