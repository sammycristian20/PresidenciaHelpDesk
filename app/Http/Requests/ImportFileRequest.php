<?php


namespace App\Http\Requests;


use App\Http\Requests\Request;
use App\Model\MailJob\QueueService;
use App\Traits\RequestJsonValidation;
use Illuminate\Http\Exceptions\HttpResponseException;

class ImportFileRequest extends Request
{
    use RequestJsonValidation;

    public function authorize()
    {
        $activeQueue = QueueService::where('status', 1)->first(['short_name']);

        if ($activeQueue) {

            if ($activeQueue->short_name === 'sync') {
                return false;
            }
        }
        return true;
    }

    public function rules()
    {
        return [
            'import_file' => 'required|file|mimes:csv,txt',
        ];
    }

    public function messages()
    {
        return [
            'import_file.required' => trans('lang.importer_file_required'),
            'import_file.file' => trans('lang.importer_file_required'),
            'import_file.mimes' => trans('lang.importer_csv_required')
        ];
    }

    protected function failedAuthorization()
    {
        throw new HttpResponseException(errorResponse(trans('lang.importer_sync_fail')));
    }
}
