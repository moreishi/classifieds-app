<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class GoogleOAuthTest extends DuskTestCase
{
    /**
     * Test that the Google sign-in button appears on the login page.
     */
    public function test_google_button_shows_on_login_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertSee('Sign in with Google')
                ->assertPresent('a[href*="auth/google"]');
        });
    }

    /**
     * Test that the Google sign-up button appears on the register page.
     */
    public function test_google_button_shows_on_register_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->assertSee('Sign up with Google')
                ->assertPresent('a[href*="auth/google"]');
        });
    }

    /**
     * Test that the Google redirect URL points to accounts.google.com.
     */
    public function test_google_redirect_goes_to_google()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login');

            // Get the href of the Google button
            $href = $browser->attribute('a[href*="auth/google"]', 'href');

            // Follow the redirect
            $browser->visit($href)
                ->assertUrlIs('https://accounts.google.com/o/oauth2/auth*');
        });
    }

    /**
     * Test callback with bad code redirects back to login.
     */
    public function test_callback_with_bad_code_redirects_to_login()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/auth/google/callback?code=fake_code_123')
                ->assertPathIs('/login')
                ->assertSee('Google sign-in failed');
        });
    }

    /**
     * Test callback without a code also redirects to login.
     */
    public function test_callback_without_code()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/auth/google/callback')
                ->assertPathIs('/login')
                ->assertSee('Google sign-in failed');
        });
    }

    /**
     * Test that the Google button appears before the login form.
     */
    public function test_google_button_before_form()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login');

            $html = $browser->source();

            // Google button HTML should appear before the email input in the source
            $buttonPos = strpos($html, 'Sign in with Google');
            $formPos = strpos($html, '<form');

            $this->assertNotFalse($buttonPos, 'Google button should exist');
            $this->assertNotFalse($formPos, 'Form should exist');
            $this->assertLessThan($formPos, $buttonPos, 'Google button should appear before the form in HTML');
        });
    }

    /**
     * Test the register page shows "or sign up with email" on desktop.
     */
    public function test_register_shows_email_option_desktop()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->assertSee('or sign up with email');
        });
    }
}
