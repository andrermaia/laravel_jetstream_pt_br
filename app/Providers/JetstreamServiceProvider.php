<?php

namespace App\Providers;

use App\Actions\Jetstream\AddTeamMember;
use App\Actions\Jetstream\CreateTeam;
use App\Actions\Jetstream\DeleteTeam;
use App\Actions\Jetstream\DeleteUser;
use App\Actions\Jetstream\InviteTeamMember;
use App\Actions\Jetstream\RemoveTeamMember;
use App\Actions\Jetstream\UpdateTeamName;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePermissions();

        Jetstream::createTeamsUsing(CreateTeam::class);
        Jetstream::updateTeamNamesUsing(UpdateTeamName::class);
        Jetstream::addTeamMembersUsing(AddTeamMember::class);
        Jetstream::inviteTeamMembersUsing(InviteTeamMember::class);
        Jetstream::removeTeamMembersUsing(RemoveTeamMember::class);
        Jetstream::deleteTeamsUsing(DeleteTeam::class);
        Jetstream::deleteUsersUsing(DeleteUser::class);
    }

    /**
     * Configure the roles and permissions that are available within the application.
     */
    protected function configurePermissions(): void
    {
        $adminPermissions = [trans('api-page.permission_create'), trans('api-page.permission_read'), trans('api-page.permission_update'), trans('api-page.permission_delete')];
        $editorPermissions = [trans('api-page.permission_create'), trans('api-page.permission_read'), trans('api-page.permission_update')];
        $defaultPermissions = [trans('api-page.permission_read')];

        Jetstream::defaultApiTokenPermissions($defaultPermissions);

        Jetstream::role('admin', trans('team-page.administrator'), $adminPermissions)->description(trans('api-page.administrator'));

        Jetstream::role('editor', trans('team-page.editor'),$editorPermissions)->description(trans('api-page.editor'));
    }
    protected function configureRoutes()
    {
        if (Jetstream::$registersRoutes) {
            Route::group([
                'namespace' => 'Laravel\Jetstream\Http\Controllers',
                'domain' => config('jetstream.domain', null),
                'prefix' => config('jetstream.prefix', config('jetstream.path')),
            ], function () {
                $this->loadRoutesFrom(__DIR__.'/../routes/'.config('jetstream.stack').'.php');
            });
        }
    }
}
