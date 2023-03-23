<?php
namespace App\Traits;
trait ListingApiTrait
{
    /*
     * Validation for listing APIs
     * 
     */
    public function ListingValidation()
    {
        $this->validate(request(), [
            'Page'          => 'integer',
            'PerPage'       => 'integer',
            'is_active'     => 'boolean',
            'search'        => 'nullable',
            'only_trashed'  => 'integer'
        ]);
        return true;
    }
/*
     * Search and Pagination for listing APIs
     * 
     */
    public function filterSearchPagination($query, $searchable_fields)
    {   
        /* Search with searchable fields */
        if (request()->search) {
            $search = request()->search;
            $query  = $query->where(function ($q) use ($search, $searchable_fields) {
                /* adding searchable fields to orwhere condition */
                foreach ($searchable_fields as $searchable_field) {
                    $q->orWhere($searchable_field, 'like', "%$search%");
                }
            });
        }

        /* Pagination */
        $count          = $query->count();
        if (request()->page && request()->perPage) {
            $page       = request()->page;
            $perPage    = request()->perPage;
            $query      = $query->skip($perPage * ($page - 1))->take($perPage);
        }
        
        return ['query' => $query, 'count' => $count];
    }
}