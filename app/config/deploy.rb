set :application, "similarseri.es"
set :domain,      "188.166.88.57"
set :deploy_to,   "/var/www/#{application}"
set :app_path,    "app"

# Using root to deploy is a bad idea, use a separate user
set :use_sudo,    false
set :user,        "deploy"

set :repository,  "http://github.com/danielsunnerberg/similarseri.es"
set :scm,         :git

set :model_manager, "doctrine"

role :web,        domain                         # Your HTTP server, Apache/etc
role :app,        domain, :primary => true       # This may be the same as your `Web` server

set  :keep_releases,  3

set :use_composer, true

set :shared_files,        ["app/config/parameters.yml"]
set :shared_children,     [app_path + "/logs", web_path + "/uploads", app_path + "/sessions"]
set :writable_dirs,       ["app/cache", "app/logs", "app/sessions"]
set :webserver_user,      "www-data"
set :permission_method,   :acl
set :use_set_permissions, true
set :dump_assetic_assets, true

default_run_options[:pty] = true

# To dump assets, they must first be installed through bower
before 'symfony:assetic:dump', 'bower:install'

# Some packages may be dependant on NPM-packages
before 'symfony:composer:install', 'npm:install'

namespace :bower do
    desc 'bower install'
    task :install do
        capifony_pretty_print "--> Installing bower components"
        invoke_command "sh -c 'cd #{latest_release} && bower install'"
        capifony_puts_ok
    end
end

namespace :npm do
    desc 'npm install'
    task :install do
        capifony_pretty_print "--> Installing NPM packages"
        invoke_command "sh -c 'cd #{latest_release} && npm install'"
        capifony_puts_ok
    end
end
