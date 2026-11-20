<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RestaurantIndexRequest;
use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Requests\UpdateRestaurantRequest;
use App\Http\Requests\UploadRestaurantImageRequest;
use App\Http\Resources\RestaurantResource;
use App\Http\Resources\UserResource;
use App\Models\Restaurant;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(RestaurantIndexRequest $request)
    {
        $params = $request->validated();
        $search = $params['search'] ?? null;
        $city = $params['city'] ?? null;

        $restaurants = Restaurant::where('is_active', true)
            ->when($city, function (EloquentBuilder $query, string $city) {
                $query->where('city', $city);
            })
            ->when($search, function (EloquentBuilder $query, string $search) {
                $query->whereLike('name', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return RestaurantResource::collection($restaurants);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRestaurantRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();
        $imagePath = null;

        try {
            $data['user_id'] = $user->id;
            $data['is_active'] = false;

            if (isset($data['image'])) {
                $imagePath = $data['image']->store('restaurants', 'public');
                $data['image'] = $imagePath;
            }
            $restaurant = Restaurant::create($data);
        } catch (Throwable $e) {
            try {
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
            } catch (Exception $deleteException) {
                Log::error('Failed to delete restaurant image after creation failure.', [
                    'image_path' => $imagePath,
                    'error' => $deleteException->getMessage(),
                ]);
            }

            throw $e;
        }

        return response()->json([
            'message' => 'Restaurant created successfully.',
            'restaurant' => new RestaurantResource($restaurant),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRestaurantRequest $request)
    {
        $data = $request->validated();

        $restaurant = $request->user()->restaurants()
            ->findOrFail($request->route('restaurant'));

        $this->authorize('update', $restaurant);

        $restaurant->update($data);

        return response()->json([
            'message' => 'Restaurant updated successfully.',
            'restaurant' => new RestaurantResource($restaurant),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function uploadImage(UploadRestaurantImageRequest $request)
    {
        $image = $request->validated();

        $restaurant = $request->user()->restaurants()->findOrFail($request->route('restaurant'));

        $currentPicture = $restaurant->image;
        $path = $image['image']->store('restaurants', 'public');

        if ($path === false) {
            Log::error('Failed to store restaurant picture', [
                'restaurant_id' => $restaurant->id,
            ]);

            throw new Exception("Failed to store restaurant's picture");
        }

        try {
            $restaurant->image = $path;
            $restaurant->save();
        } catch (Throwable $e) {
            try {
                $deleted = Storage::disk('public')->delete($path);

                if (!$deleted) {
                    Log::error('Failed to delete the picture', [
                        'path' => $path,
                    ]);
                }
            } catch (Exception $deleteException) {
                Log::error('Failed to clean up uploaded image', [
                    'path' => $path,
                    'exception' => $deleteException->getMessage(),
                ]);
            }

            Log::error('Failed to save the file path in the database', [
                'restaurant_id' => $restaurant->id,
                'exception' => $e->getMessage(),
            ]);

            throw $e;
        }

        if ($currentPicture) {
            try {
                $stillInUse = Restaurant::where('image', $currentPicture)->exists();

                if (!$stillInUse) {
                    $deleted = Storage::disk('public')->delete($currentPicture);

                    if (!$deleted) {
                        Log::error('Failed to delete the old picture', [
                            'path' => $currentPicture,
                        ]);
                    }
                }
            } catch (Exception $e) {
                Log::error('Failed to delete the old picture', [
                    'exception' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'message' => 'Image updated successfully.',
            'restaurant' => new RestaurantResource($restaurant),
        ], 200);
    }
}
