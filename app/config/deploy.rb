set :application, "similarseri.es"
set :domain,      "188.166.88.57"
set :deploy_to,   "/var/www/#{application}"
set :app_path,    "app"

set :repository,  "github.com/danielsunnerberg/similarseri.es"
set :scm,         :git

set :model_manager, "doctrine"

role :web,        domain                         # Your HTTP server, Apache/etc
role :app,        domain, :primary => true       # This may be the same as your `Web` server

set  :keep_releases,  3

