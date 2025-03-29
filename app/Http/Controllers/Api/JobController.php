<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Services\FilterBuilderService;
use App\Http\Resources\JobResource;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JobController extends Controller
{
    public function index(Request $request, FilterBuilderService $filterBuilder)
    {
        $query = Job::with(['languages', 'locations', 'categories', 'attributes', 'attributeValues']);
        
        if ($request->input('filter')) {
            try {
                $filterBuilder->applyFilters($query, $request->input('filter'));
                $jobs = $query->paginate(10);
            }
            catch (\Exception $e) {
                Log::error("Invalid filters for jobs api | filter query: {$request->input('filter')}");
                return $this->failed($e->getMessage());
            }
        }
        else {
            $jobs = $query->paginate(10);
        }

        return $this->paginate('Jobs fetched successfully.', JobResource::collection($jobs));
    }
}
