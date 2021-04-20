<?php


namespace App\Http\Requests\helpdesk\Common;

use Config;
use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ChunkUploadRequest extends Request
{
    /**
     * This method gets called automatically everytime in FormRequest class to which Request class
     * is getting inherited. So implementing this method here throws a json response and terminate
     * further processing of request which avoids a redirect (which is the default implementation).
     *
     * @param Validator $validator
     * @throw HttpResponseException
     */
    final protected function failedValidation(Validator $validator)
    {
        //sending only the first error as object
        $errors = $validator->errors()->messages();
        $formattedErrors = [];
        foreach ($errors as $key => $message) {
            $formattedErrors[$key] = $message[0];
            if ($key == 'file') {
                throw new HttpResponseException(errorResponse($message[0], 400));
            }
        }

        throw new HttpResponseException(errorResponse($formattedErrors, 412));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'chunkNumber'=>'required|numeric',
            'chunkSize'=>'required|numeric',
            'currentChunkSize'=>'required|numeric',
            'totalSize'=>'required|numeric',
            'identifier'=>'required|string',
            'filename'=>'required|string',
            'relativePath'=>'required|string',
            'totalChunks'=>'required|numeric',
        ];


        if ($this->isPublicUpload()) {
            if ($this->isChildChunk()) {
                $this->validationChildChunk($rules);
            } else {
                $allowedMimeTypes = implode(',', Config::get('filesystems.allowed_mime_types_public'));
                $rules['file'] = "required|mimes:$allowedMimeTypes";
            }
        }

        return $rules;
    }

    /**
     * If chunk upload is happening in public folder
     * @return bool
     */
    private function isPublicUpload() : bool
    {
        return strpos(\Request::getRequestUri(), "chunk/upload/public") !== false;
    }

    /**
     * Validates child chunk since child chunk has a mime type of bin, so its filename has to be validated properly. In case,
     * someone is able to create a chunk in .php extension in binary form, it should be able to block that
     * @param array $rules
     */
    private function validationChildChunk(array &$rules)
    {
        $fileExtensionByItsName = pathinfo($this->file('file')->getClientOriginalName(), PATHINFO_EXTENSION);
        $allowedMimeTypes = Config::get('filesystems.allowed_mime_types_public');
        if (!in_array($fileExtensionByItsName, $allowedMimeTypes)) {
            // throw exception for invalid file type (Suspicious file type detected)
            // not using langage here since this message is meant for someone who is trying to hack into
            throw new HttpResponseException(errorResponse('Suspicious file type detected. Aborting upload', 400));
        } else {
            $allowedMimeTypes = implode(',', $allowedMimeTypes);
            // allowing bin file type if it passes the filename validation
            $rules['file'] = "required|mimes:$allowedMimeTypes,bin";
        }
    }

    /**
     * If it is a child chunk. Child chunk comes with extension type as bin
     * @return bool
     */
    private function isChildChunk() : bool
    {
        return $this->file('file')->guessExtension() == 'bin';
    }
}
