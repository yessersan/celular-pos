<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Login;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/products')->assertRedirect('/login');
    }

    public function test_user_can_log_in_with_valid_credentials(): void
    {
        $user = User::factory()->create(['email' => 'seller@example.com', 'password' => 'Safe-Password-123']);
        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'Safe-Password-123')
            ->call('authenticate')
            ->assertRedirect(route('products'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        Livewire::test(Login::class)
            ->set('email', 'nobody@example.com')
            ->set('password', 'wrong-password')
            ->call('authenticate')
            ->assertHasErrors('email');
        $this->assertGuest();
    }
}
