<?php
namespace Deployer;

require 'recipe/laravel.php';

// Config

set('application', 'share-your-recipe');
set('repository', 'git@github.com:kmalinowska/share-your-recipe.git');

set('git_tty', false);

add('shared_files', ['.env']);
add('shared_dirs', ['storage']);
add('writable_dirs', ['bootstrap/cache', 'storage']);

// Hosts

host('x.modus.ovh')
    ->set('remote_user', 'deployer')
    ->set('ssh_arguments', [
        '-o UserKnownHostsFile=/dev/null',
        '-o StrictHostKeyChecking=no'
    ])
    ->set('port', 22678)
    ->set('deploy_path', '/var/www/share-your-recipe')
    ->set('forward_agent', true);


// Hooks

task('deploy:secrets', function () {
    $prodEnv = getenv('PROD_ENV');

    if (!empty($prodEnv)) {
        run("cat << 'EOF' > {{deploy_path}}/shared/.env\n" . $prodEnv . "\nEOF");
    } else {
        writeln("<error>Warning: Zmienna PROD_ENV jest pusta!</error>");
    }
});

task('deploy:upload-assets', function () {
    upload('public/build', '{{release_path}}/public');
});

after('deploy:update_code', 'deploy:upload-assets');

task('deploy:php-fpm:reload', function () {
    run('sudo /usr/bin/systemctl reload php8.5-fpm');
});
after('deploy:failed', 'deploy:unlock');
after('deploy:success', 'deploy:php-fpm:reload');
after('deploy:shared', 'deploy:secrets');
