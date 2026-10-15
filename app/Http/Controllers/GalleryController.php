<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();

        $photos = $request->user()->photos()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('photos.index', [
            'photos' => $photos,
            'search' => $search,
            'photoCount' => $request->user()->photos()->count(),
        ]);
    }

    public function create(): View
    {
        return view('photos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image' => ['required', 'image', 'mimes:jpeg,png,webp', 'max:5120'],
        ], [
            'image.max' => 'Ukuran foto maksimal 5 MB.',
            'image.mimes' => 'Gunakan file JPG, PNG, atau WebP.',
        ]);

        $path = $request->file('image')->store('photos', 'public');
        $request->user()->photos()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'image_path' => $path,
        ]);

        return redirect()->route('photos.index')->with('status', 'Foto berhasil ditambahkan.');
    }

    public function edit(int $photo): View
    {
        return view('photos.edit', ['photo' => $this->ownedPhoto($photo)]);
    }

    public function update(Request $request, int $photo): RedirectResponse
    {
        $photo = $this->ownedPhoto($photo);
        $validated = $request->validate([
            'title' => [$request->isMethod('PATCH') ? 'sometimes' : 'required', 'required', 'string', 'max:120'],
            'description' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'image' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,png,webp', 'max:5120'],
        ], [
            'image.max' => 'Ukuran foto maksimal 5 MB.',
            'image.mimes' => 'Gunakan file JPG, PNG, atau WebP.',
        ]);

        $attributes = array_intersect_key($validated, array_flip(['title', 'description']));
        $previousImage = $photo->image_path;

        if ($request->hasFile('image')) {
            $attributes['image_path'] = $request->file('image')->store('photos', 'public');
        }

        $photo->update($attributes);

        if (isset($attributes['image_path'])) {
            Storage::disk('public')->delete($previousImage);
        }

        return redirect()->route('photos.index')->with('status', 'Perubahan foto berhasil disimpan.');
    }

    public function destroy(int $photo): RedirectResponse
    {
        $photo = $this->ownedPhoto($photo);
        Storage::disk('public')->delete($photo->image_path);
        $photo->delete();

        return redirect()->route('photos.index')->with('status', 'Foto berhasil dihapus.');
    }

    private function ownedPhoto(int $photo): Photo
    {
        return auth()->user()->photos()->findOrFail($photo);
    }
}
