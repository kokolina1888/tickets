<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PromoterLoginTest extends TestCase
{
   use DatabaseMigrations;
    /** @test */
    function logging_in_with_valid_credentials()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('super-secret-password'),
        ]);
        $response = $this->post('/login', [
            'email' => 'jane@example.com',
            'password' => 'super-secret-password',
        ]);
        $response->assertRedirect('/backstage/concerts');
        $this->assertTrue(Auth::check());
        $this->assertTrue(Auth::user()->is($user));
    }
    /** @test */
    function logging_in_with_invalid_credentials()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('super-secret-password'),
        ]);
        $response = $this->post('/login', [
            'email' => 'jane@example.com',
            'password' => 'not-the-right-password',
        ]);
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertFalse(Auth::check());
    }
    /** @test */
    function logging_in_with_an_account_that_does_not_exist()
    {
        $this->withoutExceptionHandling();
        $response = $this->post('/login', [
            'email' => 'nobody@example.com',
            'password' => 'not-the-right-password',
        ]);
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertFalse(Auth::check());
    }
}


