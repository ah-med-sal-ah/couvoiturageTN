<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Return the currently authenticated user.
     */
    public function show(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    /**
     * Update the authenticated user's profile information.
     */
    public function updateProfile(UpdateProfileRequest $request): UserResource
    {
        $user = $request->user();
        $data = $request->validated();
        unset($data['remove_profile_photo']);

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $data['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        } elseif ($request->boolean('remove_profile_photo') && $user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
            $data['profile_photo'] = null;
        }

        $user->update($data);

        return new UserResource($user->fresh());
    }

    /**
     * Update the authenticated user's password.
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $request->user()->update([
            'password' => $request->validated('password'),
        ]);

        return response()->json([
            'message' => 'Password updated successfully.',
        ]);
    }
}
