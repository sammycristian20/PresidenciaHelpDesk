<?php


namespace App\Http\Controllers\Admin\helpdesk;

use App\Facades\Attach;
use App\Http\Controllers\Common\PhpMailController;
use App\Http\Controllers\Controller;
use App\Http\Requests\ImportFileRequest;
use App\Http\Requests\ImportMappingRequest;
use App\ImportProcessor;
use App\Jobs\ImportJob;
use App\Jobs\SendImportNotificationJob;
use App\Model\helpdesk\Import\Import;
use App\Model\helpdesk\Settings\FileSystemSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\HeadingRowImport;
use PhpOffice\PhpSpreadsheet\IOFactory;

/*
 * Handles Excel Import
 */
class FaveoImportController extends Controller
{
    /**
     * Displays Form for uploading and importing
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function importForm()
    {
        return view('themes.default1.admin.helpdesk.import.upload');
    }

    /**
     * @param ImportFileRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function processImportFile(ImportFileRequest $request)
    {
        $file = $request->file('import_file');

        $fileName = Attach::put('user_imports', $file, null, null, false, 'public');

        $spreadSheetData = (new HeadingRowImport())->toArray($fileName, FileSystemSettings::value('disk'));

        $created = Import::create(
            ['path' => $fileName, 'columns' => json_encode(array_flatten($spreadSheetData))]
        );

        return ($created)
            ? successResponse(trans('lang.importer_file_uploaded_success'),['import_id' => $created->id])
            : errorResponse(trans('lang.importer_file_upload_fail'));
    }

    /**
     * returns faveo and third party attributes for mapping columns from spreadsheet
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDetailsForProcessing(Request $request)
    {
        $returnArray = [];
        
        $import = Import::find($request->import_id);

        if ($import) {

            $columns = json_decode($import->columns, true);

            $returnArray['faveo_attributes'] = (new ImportProcessor)->getFaveoAttributes();

            $thirdPartyAtributes = (new Collection($columns))->map(function ($element) {
                $attributeObject = (object)[];
                $attributeObject->id = $element;
                $attributeObject->name = $element;
                $attributeObject->is_loginable = true;
                return $attributeObject;
            });

            $thirdPartyAtributes->push((object) ['id' => 'Do not Import', 'name' => 'Do not Import', 'is_loginable' => true]);

            $returnArray['third_party_attributes'] = $thirdPartyAtributes;

            return successResponse('', $returnArray);
        }
        return errorResponse(trans('lang.importer_import_error'));
    }

    /**
     * @param $filePathName String full path name of file to read
     * @return \PhpOffice\PhpSpreadsheet\Reader\IReader
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    private function getSpreadSheetReader($filePathName)
    {
        $inputFileType = IOFactory::identify($filePathName);

        $reader = IOFactory::createReader($inputFileType);

        $reader->setReadDataOnly(true);

        return $reader;
    }

    public function postProcessingAttributes(ImportMappingRequest $request)
    {
        $mappings = $request->faveo_attributes;

        $import = Import::find($request->import_id);

        if ($import) {

            (new PhpMailController)->setQueue();

            ImportJob::withChain([
                new SendImportNotificationJob([
                   'message' => trans('lang.importer_job_success'),
                   'to'      => \Auth::user()->id,
                   'by'      => "system",
                   'table'   => null,
                   'row_id'  => null,
                   'url'     => url('user'),
                ])
            ])->dispatch(new ImportProcessor($import->path, $mappings, json_decode($import->columns, true)));


            return successResponse(trans('lang.importer_job_queued'));
        }
        return errorResponse(trans('lang.importer_processing_fail'));
    }
}
