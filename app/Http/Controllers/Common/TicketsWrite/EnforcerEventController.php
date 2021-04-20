<?php


namespace App\Http\Controllers\Common\TicketsWrite;

use App\Http\Controllers\Common\Dependency\DependencyController;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class EnforcerEventController extends DependencyController
{
    /**
     * Gets list of elements according to the passed Type.
     * @param string $type dependency type (like help-topics, priorities etc)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle($type, Request $request)
    {
        try {
            //populating parameter variables to handle addition params in the request . For eg. search-query, limit, meta, config
            $this->initializeParameterValues($request);

            /*
             * Once class variables like config, meta, limit, search-query, userRole is populated, it can be used throughout the class
             * to give user relevant information according to the paramters passed and userType
             */
            $data = $this->handleDependencies($type);

            // in case of events, we need "any" in the list of first page
            if ((!$this->request->input('page') || $this->request->input('page') == 1) && $this->paginate) {
                $data->getCollection()->prepend(['id'=>0, 'name'=> \Lang::get('lang.any')]);
            }

            if (!$data) {
                return errorResponse(\Lang::get('lang.fails'));
            }

            return successResponse('', $data);
        } catch (\Exception $e) {
            return errorResponse($e->getMessage());
        }
    }
}
