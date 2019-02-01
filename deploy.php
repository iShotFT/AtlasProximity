<?php

namespace Deployer;

require 'recipe/laravel.php';

use Deployer\Task\Context;
use Deployer\Utility\Httpie;

set('discord_webhook', function () {
    return 'https://discordapp.com/api/webhooks/{{discord_channel}}/{{discord_token}}/slack';
});
// Deploy messages
set('discord_notify_text', function () {
    return [
        'text' => parse(':information_source: **{{user}}** is deploying branch `{{branch}}` to _{{target}}_'),
    ];
});
set('discord_success_text', function () {
    return [
        'text' => parse(':white_check_mark: Branch `{{branch}}` deployed to _{{target}}_ successfully'),
    ];
});
set('discord_failure_text', function () {
    return [
        'text' => parse(':no_entry_sign: Branch `{{branch}}` has failed to deploy to _{{target}}_'),
    ];
});
// The message
set('discord_message', 'discord_notify_text');
// Helpers
task('discord_send_message', function () {
    $message = get(get('discord_message'));
    Httpie::post(get('discord_webhook'))->body($message)->send();
});
// Tasks
desc('Just notify your Discord channel with all messages, without deploying');
task('discord:test', function () {
    set('discord_message', 'discord_notify_text');
    invoke('discord_send_message');
    set('discord_message', 'discord_success_text');
    invoke('discord_send_message');
    set('discord_message', 'discord_failure_text');
    invoke('discord_send_message');
})->once()->shallow();
desc('Notify Discord');
task('discord:notify', function () {
    set('discord_message', 'discord_notify_text');
    invoke('discord_send_message');
})->once()->shallow()->isPrivate();
desc('Notify Discord about deploy finish');
task('discord:notify:success', function () {
    set('discord_message', 'discord_success_text');
    invoke('discord_send_message');
})->once()->shallow()->isPrivate();
desc('Notify Discord about deploy failure');
task('discord:notify:failure', function () {
    set('discord_message', 'discord_failure_text');
    invoke('discord_send_message');
})->once()->shallow()->isPrivate();
// Discord
//https://discordapp.com/api/webhooks/538996091576778753/4VMHrmqrBzsbSRr7_Hqdlfg6gFhQixFb50eQPC3frp4-7eiaek77Gyp3IuOgQpID7QW_
// https://discordapp.com/api/webhooks/539068559574171648/5wfkG0CD9bLcIvPxEQp6M2QgN1SQwRKBfuD1jkQ6Ue3rjEC9xWlPJW5XwS54X689bTSC
set('discord_channel', '539068559574171648');
set('discord_token', '5wfkG0CD9bLcIvPxEQp6M2QgN1SQwRKBfuD1jkQ6Ue3rjEC9xWlPJW5XwS54X689bTSC');
set('user', 'iShot');

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
    'laravel-echo-server.json',
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
    run('sudo /usr/sbin/service php7.3-fpm reload');
});

task('reload:atlascctv', function () {
    run('sudo /bin/systemctl restart atlascctv');
});

task('reload:queue', function () {
    run('cd {{release_path}} && php artisan queue:restart');
});

task('reload:larecipe', function () {
    run('cd {{release_path}} && php artisan larecipe:install');
});

set('bin/npm', function () {
    return run('which npm');
});

task('npm:install', function () {
    if (has('previous_release')) {
        if (test('[ -d {{previous_release}}/node_modules ]')) {
            run('cp -R {{previous_release}}/node_modules {{release_path}}');
        }
    }
    run("cd {{release_path}} && {{bin/npm}} install");
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.
before('deploy:symlink', 'artisan:migrate');
after('deploy:update_code', 'npm:install');
after('success', 'discord:notify:success');
after('deploy:failed', 'discord:notify:failure');
before('deploy', 'discord:notify');
after('deploy', 'reload:larecipe');
after('deploy', 'reload:php-fpm');
after('deploy', 'reload:atlascctv');
after('deploy', 'reload:queue');
after('rollback', 'reload:php-fpm');
after('rollback', 'reload:atlascctv');

