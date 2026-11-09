<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_register_as_a_peminjam(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Pengguna Baru',
            'email' => 'baru@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('login'))
            ->assertSessionHas('success', 'Registrasi berhasil. Silakan masuk.');

        $user = User::where('email', 'baru@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('peminjam', $user->role);
        $this->assertTrue(password_verify('password123', $user->password));
    }

    public function test_registration_rejects_an_email_that_is_already_registered(): void
    {
        User::create([
            'name' => 'Pengguna Terdaftar',
            'email' => 'terdaftar@example.com',
            'password' => 'password123',
            'role' => 'peminjam',
        ]);

        $response = $this->from(route('register'))->post(route('register'), [
            'name' => 'Pengguna Baru',
            'email' => 'terdaftar@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('register'))
            ->assertSessionHasErrors('email');
        $this->assertSame(1, User::where('email', 'terdaftar@example.com')->count());
    }

    public function test_login_page_links_to_registration(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee(route('register'));
    }
}
