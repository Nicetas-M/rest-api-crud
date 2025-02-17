<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Resources\ProductResource;
use App\Interfaces\RepositoryInterface;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    private RepositoryInterface $repositoryInterface;

    public function __construct(RepositoryInterface $repositoryInterface) {
        $this->repositoryInterface = $repositoryInterface;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->repositoryInterface->index();
        return ApiResponseClass::sendResponse(ProductResource::collection($data), 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $details = [
            'name' => $request->name,
            'details' => $request->details
        ];
        DB::beginTransactions();
        try {
            $product = $this->repositoryInterface->store($details);
            DB::commit();
            return ApiResponseClass::sendResponse(new ProductResource($product), 'Product Create Successful', 201);
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = $this->repositoryInterface->getById($id);
        return ApiResponseClass::sendResponse(new ProductResource($product), '', 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, $id)
    {
        $updateDetails = [
            'name' => $request->name,
            'details' => $request->details
        ];
        DB::beginTransaction();
        try {
            $product = $this->repositoryInterface->update($updateDetails, $id);

            DB::commit();
            return ApiResponseClass::sendResponse( 'Product Update Successful', '', 201);
        } catch (\Exception $e) {
            return ApiResponseClass::rollback($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->repositoryInterface->delete($id);

        return ApiResponseClass::sendResponse('Product Delete Successful', '', 204);
    }
}
