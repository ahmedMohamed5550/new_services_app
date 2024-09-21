<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLocationRequest;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    use ApiResponseTrait;
    /**
     * @OA\Post(
     *     path="/api/location/store",
     *     summary="Add location to user",
     *     tags={"location"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="city",
     *                     type="string",
     *                     description="City name"
     *                 ),
     *                 @OA\Property(
     *                     property="bitTitle",
     *                     type="string",
     *                     description="Title or nickname for the location"
     *                 ),
     *                 @OA\Property(
     *                     property="street",
     *                     type="string",
     *                     description="Street name"
     *                 ),
     *                 @OA\Property(
     *                     property="specialMarque",
     *                     type="string",
     *                     description="Special landmark near the location"
     *                 ),
     *                 @OA\Property(
     *                     property="zipCode",
     *                     type="string",
     *                     description=" city zip code"
     *                 ),
     *                 @OA\Property(
     *                     property="lat",
     *                     type="number",
     *                     format="float",
     *                     description="Latitude coordinate"
     *                 ),
     *                 @OA\Property(
     *                     property="long",
     *                     type="number",
     *                     format="float",
     *                     description="Longitude coordinate"
     *                 ),
     *                 @OA\Property(
     *                     property="user_id",
     *                     type="integer",
     *                     description="User ID"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Location added successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Added Location successfully"),
     *             @OA\Property(property="location", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */


     public function store(StoreLocationRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $location = Location::create($validatedData);

            return $this->apiResponse('location updated Successfully' , 200 , new LocationResource($location));


        } catch (Exception $e) {
            return $this->apiResponse('An error occurred', 500,$e->getMessage());
        }
    }



    /**
     * @OA\Get(
     *     path="/api/location/showUsersLocation/{user_id}",
     *     summary="Show all locations",
     *     description="Show all locations for a user by user ID",
     *     tags={"location"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         description="ID of the user to show all locations",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Show locations successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="locations", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No locations found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No locations found")
     *         )
     *     )
     * )
     */

     public function showUsersLocation($id)
     {
         $locations = Location::where('user_id', $id)->with('user')->get();

         if ($locations->isNotEmpty()) {
             return $this->apiResponse('Show user locations successfully', 200, LocationResource::collection($locations));
         } else {
             return $this->apiResponse('No location found', 404);
         }
     }



    /**
     * @OA\Post(
     *     path="/api/location/update/{id}",
     *     summary="Update user location",
     *     tags={"location"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the location",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="city",
     *                     type="string",
     *                     description="City name"
     *                 ),
     *                 @OA\Property(
     *                     property="bitTitle",
     *                     type="string",
     *                     description="Title or nickname for the location"
     *                 ),
     *                 @OA\Property(
     *                     property="street",
     *                     type="string",
     *                     description="Street name"
     *                 ),
     *                 @OA\Property(
     *                     property="specialMarque",
     *                     type="string",
     *                     description="Special landmark near the location"
     *                 ),
     *                 @OA\Property(
     *                     property="zipCode",
     *                     type="string",
     *                     description=" city zip code"
     *                 ),
     *                 @OA\Property(
     *                     property="lat",
     *                     type="number",
     *                     format="float",
     *                     description="Latitude coordinate"
     *                 ),
     *                 @OA\Property(
     *                     property="long",
     *                     type="number",
     *                     format="float",
     *                     description="Longitude coordinate"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Location updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Updated Location successfully"),
     *             @OA\Property(property="location", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Location not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Location not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation errors"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */


 public function update(Request $request, $id){
    $request->validate([
        'city' => 'required|string|max:255',
        'bitTitle' => 'required|string|max:255',
        'street' => 'required|string|max:255',
        'specialMarque' => 'nullable|string|max:255',
        'zipCode' => 'nullable|string|max:255',
        'lat' => 'nullable|numeric',
        'long' => 'nullable|numeric',
    ]);

    try{
        $location = Location::with('user')->find($id);

        if ($location) {

            $location->update([
                'city' => $request->city ?? $location->city,
                'bitTitle' => $request->bitTitle ?? $location->bitTitle,
                'street' => $request->street ?? $location->street,
                'specialMarque' => $request->specialMarque ?? $location->specialMarque,
                'zipCode' => $request->zipCode ?? $location->zipCode,
                'lat' => $request->lat ?? $location->lat,
                'long' => $request->long ?? $location->long,
            ]);

            return $this->apiResponse('Updated Location successfully' , 200 , new LocationResource($location));
        }
        else
        {
            return $this->apiResponse('no location found' , 404);
        }
    }
    catch (Exception $e) {
        return $this->apiResponse('An error occurred', 500,$e->getMessage());
    }
    }




    /**
     * @OA\Delete(
     *     path="/api/location/destroy/{id}",
     *     summary="Delete a location",
     *     description="Delete location for a user by location ID",
     *     tags={"location"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the location to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Location deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Delete Location successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Location not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Location not found")
     *         )
     *     )
     * )
     */

    public function destroy($id){
        $location=Location::find($id);

        if($location){
            $location->delete();
            return $this->apiResponse('Deleted Location successfully' , 200);
        }
        else{
            return $this->apiResponse('no location found' , 404);
        }
    }
}
