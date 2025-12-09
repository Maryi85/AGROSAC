<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        return view('worker.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            $photo = $request->file('photo');
            $originalName = $photo->getClientOriginalName();
            $extension = $photo->getClientOriginalExtension();
            $safeName = preg_replace('/[^A-Za-z0-9\-_]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
            $photoName = time() . '_' . $safeName . '.' . $extension;

            $directory = storage_path('app/public/photos/users');
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            $path = Storage::disk('public')->putFileAs('photos/users', $photo, $photoName);
            if ($path) {
                $data['photo'] = $path;
            }
        }

        $user->update($data);

        return back()->with('status', 'Perfil actualizado correctamente.');
    }
}

