<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function showLogin(): View
    {
        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $validUsername = hash_equals((string) config('admin.username'), $credentials['username']);
        $validPassword = hash_equals((string) config('admin.password'), $credentials['password']);

        if (! $validUsername || ! $validPassword) {
            return back()->withErrors([
                'username' => 'Username atau password admin tidak cocok.',
            ])->onlyInput('username');
        }

        $request->session()->regenerate();
        $request->session()->put('admin_authenticated', true);

        return redirect()->route('admin.dashboard');
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();

        $photos = Photo::query()
            ->with('user')
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('user', function (Builder $userQuery) use ($search): void {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('updated_at')
            ->paginate(12)
            ->withQueryString();

        return view('admin.index', [
            'photos' => $photos,
            'search' => $search,
            'photoCount' => Photo::count(),
            'blockedPhotoCount' => Photo::where('is_blocked', true)->count(),
            'userCount' => User::count(),
        ]);
    }

    public function users(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();

        $users = User::query()
            ->select(['id', 'name', 'email', 'created_at'])
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users', [
            'users' => $users,
            'search' => $search,
        ]);
    }

    public function destroyUser(User $user): RedirectResponse
    {
        $imagePaths = $user->photos()->pluck('image_path')->all();

        if ($imagePaths !== [] && ! Storage::disk('public')->delete($imagePaths)) {
            throw new \RuntimeException('Failed to delete user photo files.');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('status', 'Akun pengguna dan seluruh postingnya berhasil dihapus.');
    }

    public function updatePhotoStatus(Request $request, Photo $photo): RedirectResponse
    {
        $validated = $request->validate([
            'is_blocked' => ['required', 'boolean'],
        ]);

        $photo->update($validated);

        $message = $photo->is_blocked
            ? 'Posting berhasil diblokir.'
            : 'Posting berhasil diaktifkan kembali.';

        return redirect()->route('admin.dashboard')->with('status', $message);
    }

    public function destroyPhoto(Photo $photo): RedirectResponse
    {
        Storage::disk('public')->delete($photo->image_path);
        $photo->delete();

        return redirect()->route('admin.dashboard')->with('status', 'Posting dan fotonya berhasil dihapus.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('admin_authenticated');
        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
