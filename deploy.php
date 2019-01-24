<?php

namespace Deployer;

require 'recipe/laravel.php';
require 'recipe/npm.php';

// Project name
set('application', 'AtlasProximity');

// Project repository
set('repository', 'git@github.com:iShotFT/AtlasProximity.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Shared files/dirs between deploys 
add('shared_files', [
    '.env',
    'resources/js/bot/config.json',
]);

add('shared_dirs', [
    'storage',
]);

// Writable dirs by web server 
add('writable_dirs', [
    'bootstrap/cache',
    'storage',
    'storage/app',
    'storage/app/public',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
]);
set('allow_anonymous_stats', false);

// Hosts
host('46.101.104.25')->user('deployer')->identityFile('~/.ssh/deployerkey')->set('deploy_path', '/var/www/atlas.heraldofdeath.com');

// Tasks

task('build', function () {
    run('cd {{release_path}} && build');
});

task('reload:php-fpm', function () {
    run('sudo /usr/sbin/service php7.2-fpm reload');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.
before('deploy:symlink', 'artisan:migrate');
after('deploy:update_code', 'npm:install');
after('deploy', 'reload:php-fpm');
after('rollback', 'reload:php-fpm');

