<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_before_opening_the_gallery(): void
    {
        $this->get('/photos')->assertRedirect('/login');
    }

    public function test_a_user_can_register_and_is_signed_in(): void
    {
        $this->post('/register', [
            'name' => 'Rani Putri',
            'email' => 'rani@example.com',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertRedirect('/photos');

        $user = User::where('email', 'rani@example.com')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Hash::check('rahasia123', $user->password));
    }

    public function test_a_user_can_sign_in_and_sign_out(): void
    {
        $user = User::factory()->create([
            'email' => 'rani@example.com',
            'password' => 'rahasia123',
        ]);

        $this->post('/login', [
            'email' => 'rani@example.com',
            'password' => 'rahasia123',
        ])->assertRedirect('/photos');

        $this->assertAuthenticatedAs($user);

        $this->post('/logout')->assertRedirect('/login');

        $this->assertGuest();
    }

    public function test_a_user_cannot_sign_in_with_an_incorrect_password(): void
    {
        User::factory()->create([
            'email' => 'rani@example.com',
            'password' => 'rahasia123',
        ]);

        $this->from('/login')
            ->post('/login', [
                'email' => 'rani@example.com',
                'password' => 'kata-sandi-salah',
            ])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_a_user_cannot_register_with_an_existing_email_or_unconfirmed_password(): void
    {
        User::factory()->create(['email' => 'rani@example.com']);

        $this->from('/register')
            ->post('/register', [
                'name' => 'Rani Putri',
                'email' => 'rani@example.com',
                'password' => 'rahasia123',
                'password_confirmation' => 'rahasia123',
            ])
            ->assertRedirect('/register')
            ->assertSessionHasErrors('email');

        $this->from('/register')
            ->post('/register', [
                'name' => 'Sari Putri',
                'email' => 'sari@example.com',
                'password' => 'rahasia123',
                'password_confirmation' => 'tidak-sama',
            ])
            ->assertRedirect('/register')
            ->assertSessionHasErrors('password');
    }

    public function test_authenticated_users_can_open_the_gallery_and_photo_forms(): void
    {
        $user = User::factory()->create();
        $photo = $user->photos()->create([
            'title' => 'Pagi di taman',
            'description' => 'Cahaya pertama.',
            'image_path' => 'photos/pagi.png',
        ]);

        $this->actingAs($user)
            ->get('/photos')
            ->assertOk()
            ->assertSeeText('Pagi di taman');

        $this->get('/photos/create')->assertOk()->assertSeeText('Tambah momen.');
        $this->get('/photos/'.$photo->id.'/edit')->assertOk()->assertSeeText('Atur cerita.');
    }

    public function test_users_can_manage_only_their_own_photos(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create();
        $anotherUser = User::factory()->create();
        $otherPhoto = $anotherUser->photos()->create([
            'title' => 'Foto rahasia',
            'description' => null,
            'image_path' => 'photos/private.jpg',
        ]);

        $this->actingAs($owner)
            ->post('/photos', [
                'title' => 'Senja di pantai',
                'description' => 'Langit sore setelah hujan.',
                'image' => UploadedFile::fake()->createWithContent(
                    'senja.png',
                    base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/lZ8AAAAASUVORK5CYII=')
                ),
            ])
            ->assertRedirect('/photos');

        $photo = $owner->photos()->firstOrFail();
        Storage::disk('public')->assertExists($photo->image_path);
        $this->assertDatabaseHas('photos', ['id' => $photo->id, 'user_id' => $owner->id]);
        $this->get('/photos/'.$otherPhoto->id.'/edit')->assertNotFound();

        $this->put('/photos/'.$photo->id, [
            'title' => 'Senja setelah hujan',
            'description' => 'Pantai yang sama, cerita baru.',
        ])->assertRedirect('/photos');

        $this->assertDatabaseHas('photos', [
            'id' => $photo->id,
            'title' => 'Senja setelah hujan',
        ]);

        $this->delete('/photos/'.$photo->id)->assertRedirect('/photos');
        $this->assertDatabaseMissing('photos', ['id' => $photo->id]);
        Storage::disk('public')->assertMissing($photo->image_path);
    }

    public function test_users_can_partially_update_a_photo_with_patch(): void
    {
        $user = User::factory()->create();
        $photo = $user->photos()->create([
            'title' => 'Pagi di taman',
            'description' => 'Cahaya pertama.',
            'image_path' => 'photos/pagi.png',
        ]);

        $this->actingAs($user)
            ->patch('/photos/'.$photo->id, [
                'description' => 'Cahaya setelah hujan.',
            ])
            ->assertRedirect('/photos');

        $this->assertDatabaseHas('photos', [
            'id' => $photo->id,
            'title' => 'Pagi di taman',
            'description' => 'Cahaya setelah hujan.',
            'image_path' => 'photos/pagi.png',
        ]);
    }

    public function test_updating_a_photo_image_replaces_the_stored_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('photos/old.png', 'old image');

        $user = User::factory()->create();
        $photo = $user->photos()->create([
            'title' => 'Pagi di taman',
            'description' => 'Cahaya pertama.',
            'image_path' => 'photos/old.png',
        ]);

        $this->actingAs($user)
            ->put('/photos/'.$photo->id, [
                'title' => 'Pagi di taman',
                'description' => 'Cahaya pertama.',
                'image' => UploadedFile::fake()->createWithContent(
                    'new.png',
                    base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/lZ8AAAAASUVORK5CYII=')
                ),
            ])
            ->assertRedirect('/photos');

        $photo->refresh();
        $this->assertNotSame('photos/old.png', $photo->image_path);
        Storage::disk('public')->assertExists($photo->image_path);
        Storage::disk('public')->assertMissing('photos/old.png');
    }
}
