<?php namespace tests\BeatSwitch\Lock\Integrations\Laravel;

use Orchestra\Testbench\TestCase;
use Illuminate\Auth\GenericUser;
use BeatSwitch\Lock\Callers\Caller;
use BeatSwitch\Lock\LockAware;
use BeatSwitch\Lock\Lock;
use BeatSwitch\Lock\Manager;

class GenericLockUser extends GenericUser implements Caller {

    use LockAware;

    /**
     * @return string
     */
    public function getCallerType()
    {
        return 'users';
    }

    /**
     * @return int
     */
    public function getCallerId()
    {
        return 1;
    }

    /**
     * @return array
     */
    public function getCallerRoles()
    {
        return ['user'];
    }
}

class LockServiceProviderTest extends TestCase {

    protected function getPackageProviders($app)
    {
        return ['BeatSwitch\Lock\Integrations\Laravel\LockServiceProvider'];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']['lock.permissions'] = function (Manager $manager, Lock $caller) {
            //
        };
    }

    /**
     * Test that the lock service provider sets the lock on the authenticated user
     */
    public function testLockResolutionUsesAuthenticatedUser()
    {
        $this->app->make('auth')->setUser(new GenericLockUser(['id' => 'generic']));

        // resolve lock instance  after setting/logging in the user - like in a real application
        $lock = $this->app->make('lock');

        $this->assertInstanceOf(
            'tests\BeatSwitch\Lock\Integrations\Laravel\GenericLockUser', $lock->getCaller());
    }

    /**
     * Test that the lock service provider sets the lock on the authenticated user
     */
    public function testLockResolutionWhenUserIsNotAuthenticated()
    {
        // resolve lock instance  after setting/logging in the user - like in a real application
        $lock = $this->app->make('lock');

        $this->assertInstanceOf(
            'BeatSwitch\Lock\Callers\SimpleCaller', $lock->getCaller());
    }
}