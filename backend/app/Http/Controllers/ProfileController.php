<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UploadProfilePictureRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        return new UserResource($user);
    }

    public function update(UpdateProfileRequest $request)
    {
        $data = $request->validated();

        $user = $request->user();

        $user->update($data);

        return new UserResource($user);
    }

    public function uploadProfilePicture(UploadProfilePictureRequest $request)
    {
        $picture = $request->validated();

        $path = $picture['profile_picture']->store('profile_pictures', 'public');

        if ($path === false) {
            Log::error('Failed to store profile picture', [
                'user_id' => $request->user()->id,
            ]);

            throw new Exception('Failed to store profile picture');
        }

        $user = $request->user();
        $currentPicture = $user->profile_picture;

        try {
            $user['profile_picture'] = $path;
            $user->save();
        } catch (Exception $e) {
            Storage::disk('public')->delete($path);

            Log::error('Failed to save the file path in the database', [
                'exception' => $e->getMessage(),
            ]);

            throw $e;
        }

        if ($currentPicture) {
            try {
                $stillInUse = User::where('profile_picture', $currentPicture)->exists();

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
            'message' => 'Profile picture updated successfully!',
            'user' => new UserResource($user),
        ]);
    }
}
