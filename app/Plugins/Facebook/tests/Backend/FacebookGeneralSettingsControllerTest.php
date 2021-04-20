<?php

namespace App\Plugins\Facebook\tests\Backend;

use App\Plugins\Facebook\Model\FacebookGeneralSettings;
use Tests\AddOnTestCase;

class FacebookGeneralSettingsControllerTest extends AddOnTestCase
{
    public function testSettingsPageMethodReturnsProperViewForAdmins()
    {
        $this->getLoggedInUserForWeb('admin');
        $this->get(route('facebook.security.settings'))->assertOk()
            ->assertViewIs('facebook::secure-facebook');
    }

    public function testSettingsPageMethodNotAccessibleByNonAdmins()
    {
        $this->getLoggedInUserForWeb();
        $this->get(route('facebook.security.settings'))->assertStatus(302);
    }

    public function testStoreMethodCreatesSecuritySettingsSuccessfully()
    {
        $this->getLoggedInUserForWeb('admin');

        $payload = ['fb_secret' => 'zxsw1edrt', 'hub_verify_token' => 'swsdfer'];

        $response = $this->post(route('facebook.security.create'), $payload);

        $response->assertJsonFragment(['message' => trans('Facebook::lang.facebook_security_settings_saved')]);

        $this->assertDatabaseHas('facebook_general_details', $payload);
    }

    public function testStoreMethodEnforcesProperValidationRules()
    {
        $this->getLoggedInUserForWeb('admin');

        //check for empty fields
        $this->post(route('facebook.security.create'), [])
            ->assertJsonFragment(
                [
                    "fb_secret" => trans('Facebook::lang.facebook_secret_required'),
                    "hub_verify_token" => trans('Facebook::lang.facebook_hub_verify_token_required')
                ]
            );

        //checking unique
        $settings = factory(FacebookGeneralSettings::class)->create();

        $this->post(route('facebook.security.create'), [
            'fb_secret' => $settings->fb_secret,
            'hub_verify_token' => $settings->hub_verify_token
        ])->assertJsonFragment(
            [
                "fb_secret" => trans('Facebook::lang.facebook_secret_not_unique'),
                "hub_verify_token" => trans('Facebook::lang.facebook_hub_verify_token_not_unique')
            ]
        );

    }

    public function testStoreMethodFailsIfNonAdminUsersTryToCreateAddSecuritySeettings()
    {
        $this->getLoggedInUserForWeb();

        $this->post(route('facebook.security.create'), [])->assertStatus(302);

    }

    public function testIndexMethodReturnsAllSettingsAsJsonSuccessfully()
    {
        $this->getLoggedInUserForWeb('admin');

        $settings = factory(FacebookGeneralSettings::class)->create();

        $this->get(route('facebook.security.index'))
            ->assertOk()
            ->assertJsonFragment(["fb_secret" => $settings->fb_secret ,"hub_verify_token" => $settings->hub_verify_token]);

    }

    public function testIndexMethodFailsWhenNoSecuritySettingsPresent()
    {
        $this->getLoggedInUserForWeb('admin');
        $this->get(route('facebook.security.index'))
            ->assertStatus(400)
            ->assertJsonFragment(["message" => trans('Facebook::lang.facebook_no_security_settings')]);
    }

    public function testUpdateMethodSuccessfullyUpdatesSecuritySettingsData()
    {
        $this->getLoggedInUserForWeb('admin');

        $settings = factory(FacebookGeneralSettings::class)->create();

        $payload = ['fb_secret' => 'zxsw1edrt', 'hub_verify_token' => 'swsdfer'];

        $this->put(route('facebook.security.update', $settings->id), $payload)->assertOk()
            ->assertJsonFragment(['message' => trans('Facebook::lang.facebook_security_settings_saved')]);

        $this->assertDatabaseHas('facebook_general_details', $payload);
    }

    public function testUpdateMethodFailsWhenInvalidIdIsSupplied()
    {
        $this->getLoggedInUserForWeb('admin');

        factory(FacebookGeneralSettings::class)->create();

        $payload = ['fb_secret' => 'zxsw1edrt', 'hub_verify_token' => 'swsdfer'];

        $this->put(route('facebook.security.update', 99999999999), $payload)->assertStatus(400)
            ->assertJsonFragment(['message' => trans('Facebook::lang.facebook_security_settings_save_error')]);
    }

    public function testDestroyMethodFailsWhenInvalidIdIsSupplied()
    {
        $this->getLoggedInUserForWeb('admin');

        $settings = factory(FacebookGeneralSettings::class)->create();

        $this->delete(route('facebook.security.delete', 99999999999))->assertStatus(400)
            ->assertJsonFragment(['message' => trans('Facebook::lang.facebook_security_settings_delete_error')]);

        $this->assertDatabaseHas('facebook_general_details', ['fb_secret' => $settings->fb_secret]);
    }

    public function testDestroyMethodDestroysTheSettings()
    {
        $this->getLoggedInUserForWeb('admin');

        $settings = factory(FacebookGeneralSettings::class)->create();

        $this->delete(route('facebook.security.delete', $settings->id))->assertOk()
            ->assertJsonFragment(['message' => trans('Facebook::lang.facebook_security_settings_deleted')]);

        $this->assertDatabaseMissing('facebook_general_details', ['fb_secret' => $settings->fb_secret]);
    }
}
