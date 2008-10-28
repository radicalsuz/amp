set :application, "amp"
set :repository,  "https://svn.radicaldesigns.org/amp/trunk"

set :user, "amp"

# If you aren't deploying to /u/apps/#{application} on the target
# servers (which is the default), you can specify the actual location
# via the :deploy_to variable:
# set :deploy_to, "/var/www/#{application}"
set :deploy_to, "/home/#{application}/source"
set :deploy_via, :remote_cache
set :current_dir, "../public_html"
set :use_sudo, false

role(:app,  "gertie.radicaldesigns.org", "sft.slice.radicaldesigns.org",  "sadie.radicaldesigns.org", "grace.radicaldesigns.org", "cindy.radicaldesigns.org" )

role :db,   "gertie.radicaldesigns.org"
#role :db,  "sadie.radicaldesigns.org", :primary => true
#role :db,  "sft.slice.radicaldesigns.org"
after "deploy:update_code", "deploy:symlink_shared"
after "deploy:update_code", "deploy:www_group"

namespace :deploy do
  task :symlink_shared, :roles => :app, :except => { :no_symlink => true } do
    invoke_command "ln -nfs #{shared_path}/.htaccess #{release_path}/.htaccess"
  end

  task :www_group, :roles => :app do
    #if capture( 'id -Gn' ).include? 'www-data'
    begin
    invoke_command "chgrp -h www-data #{current_path}"
    rescue
    end
    #end
  end

  task :restart, :roles => :app do
    puts "restart requires root access to the box"
    #invoke_command "apachectl graceful"
  end

  task :migrate, :roles => :db do
    #invoke_command "rake -f #{File.expand_path(current_path)}/config/amp_tasks.rb amp:migrate:sql"
  end

end

