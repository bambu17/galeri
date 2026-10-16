<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminModerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_admin_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_admin_can_sign_in_with_the_configured_credentials(): void
    {
        $this->post('/admin/login', [
            'username' => 'admin',
            'password' => 'admin',
        ])->assertRedirect('/admin');

        $this->get('/admin')->assertOk()->assertSeeText('Aktivitas user');
    }

    public function test_invalid_admin_credentials_are_rejected(): void
    {
        $this->from('/admin/login')
            ->post('/admin/login', [
                'username' => 'admin',
                'password' => 'wrong-password',
            ])
            ->assertRedirect('/admin/login')
            ->assertSessionHasErrors('username');
    }

    public function test_admin_can_sign_out_and_loses_access_to_admin_routes(): void
    {
        $this->withAdminSession()
            ->post('/admin/logout')
            ->assertRedirect('/admin/login');

        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_admin_can_review_user_activity_and_block_or_restore_a_post(): void
    {
        $user = User::factory()->create(['name' => 'Rani Putri']);
        $photo = $user->photos()->create([
            'title' => 'Foto pantai',
            'description' => 'Liburan akhir pekan.',
            'image_path' => 'photos/pantai.jpg',
        ]);

        $this->withAdminSession()
            ->get('/admin')
            ->assertOk()
            ->assertSeeText('Rani Putri')
            ->assertSeeText('Foto pantai')
            ->assertSeeText('Aktivitas user');

        $this->patch('/admin/photos/'.$photo->id.'/status', ['is_blocked' => true])
            ->assertRedirect('/admin');

        $this->assertDatabaseHas('photos', ['id' => $photo->id, 'is_blocked' => true]);
        $this->actingAs($user)->get('/photos')->assertDontSeeText('Foto pantai');
        $this->get('/photos/'.$photo->id.'/edit')->assertNotFound();

        $this->withAdminSession()
            ->patch('/admin/photos/'.$photo->id.'/status', ['is_blocked' => false])
            ->assertRedirect('/admin');

        $this->assertDatabaseHas('photos', ['id' => $photo->id, 'is_blocked' => false]);
    }

    public function test_regular_users_cannot_open_admin_routes(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin')
            ->assertRedirect('/admin/login');

        $this->get('/admin/users')->assertRedirect('/admin/login');
    }

    public function test_admin_can_view_user_account_names_and_emails_without_passwords(): void
    {
        $user = User::factory()->create([
            'name' => 'Rani Putri',
            'email' => 'rani@example.com',
            'password' => 'rahasia-user',
        ]);

        $this->withAdminSession()
            ->get('/admin/users')
            ->assertOk()
            ->assertSeeText('Rani Putri')
            ->assertSeeText('rani@example.com')
            ->assertDontSee($user->password)
            ->assertDontSee('rahasia-user');
    }

    public function test_admin_can_search_user_accounts_by_email(): void
    {
        User::factory()->create([
            'name' => 'Rani Putri',
            'email' => 'rani@example.com',
        ]);
        User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
        ]);

        $this->withAdminSession()
            ->get('/admin/users?search=rani%40example.com')
            ->assertOk()
            ->assertSeeText('Rani Putri')
            ->assertDontSeeText('Budi Santoso');
    }

    public function test_admin_can_delete_a_user_account_and_all_of_its_photos(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('photos/rani.jpg', 'rani photo');
        Storage::disk('public')->put('photos/rani-second.jpg', 'another rani photo');
        Storage::disk('public')->put('photos/budi.jpg', 'budi photo');

        $user = User::factory()->create([
            'name' => 'Rani Putri',
            'email' => 'rani@example.com',
        ]);
        $photo = $user->photos()->create([
            'title' => 'Foto Rani',
            'description' => null,
            'image_path' => 'photos/rani.jpg',
        ]);
        $secondPhoto = $user->photos()->create([
            'title' => 'Foto kedua Rani',
            'description' => null,
            'image_path' => 'photos/rani-second.jpg',
        ]);
        $otherUserPhoto = User::factory()->create()->photos()->create([
            'title' => 'Foto Budi',
            'description' => null,
            'image_path' => 'photos/budi.jpg',
        ]);

        $this->withAdminSession()
            ->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users'))
            ->assertSessionHas('status', 'Akun pengguna dan seluruh postingnya berhasil dihapus.');

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('photos', ['id' => $photo->id]);
        $this->assertDatabaseMissing('photos', ['id' => $secondPhoto->id]);
        $this->assertDatabaseHas('photos', ['id' => $otherUserPhoto->id]);
        Storage::disk('public')->assertMissing('photos/rani.jpg');
        Storage::disk('public')->assertMissing('photos/rani-second.jpg');
        Storage::disk('public')->assertExists('photos/budi.jpg');
    }

    public function test_regular_users_cannot_delete_user_accounts(): void
    {
        $user = User::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('admin.users.destroy', $user))
            ->assertRedirect('/admin/login');

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_admin_can_delete_a_post_and_its_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('photos/remove.jpg', 'photo contents');

        $photo = User::factory()->create()->photos()->create([
            'title' => 'Foto yang dihapus',
            'description' => null,
            'image_path' => 'photos/remove.jpg',
        ]);

        $this->withAdminSession()
            ->delete('/admin/photos/'.$photo->id)
            ->assertRedirect('/admin');

        $this->assertDatabaseMissing('photos', ['id' => $photo->id]);
        Storage::disk('public')->assertMissing('photos/remove.jpg');
    }

    private function withAdminSession(): static
    {
        return $this->withSession(['admin_authenticated' => true]);
    }
}
