class Log
  def initialize( filename=nil )
    if filename
      @file = File.open filename, 'a'
      at_exit { @file.close }
    else
      @file = $stdout
    end
  end

  def write( text )
    text << "\n"
    @file << text
    text
  end
end

unless defined?( SOURCE_PATH )
  SOURCE_PATH = File.dirname(__FILE__) + "/.."
  SITE_PATTERN = File.expand_path( SOURCE_PATH + "/../../%s/public_html" )
  #SITE_PATTERN = File.expand_path( SOURCE_PATH + "/../%s" )
  MIGRATIONS_PATH = File.dirname(__FILE__) + "/../sql"
end

namespace :amp do
    
  def connection
    require File.dirname(__FILE__) + '/amp_databases'
    AmpDatabase
  end

  def log(text)
    @log ||= Log.new ENV['LOG_PATH']
    @log.write text
  end

  desc "list AMP databases"
  task :list do
    log connection.find.keys.join("\n") + "\n"
  end

  desc "migrate all sites to a new revision"
  namespace :migrate do
    desc "run sql migrations for the latest version"
    task :sql do
      databases.each do |db_name, db|
        sql_updates.each { |sql_file| run_sql( db,sql_file ) }
      end
    end

    desc "run one-time sql migrations for the latest version"
    task :sql_one_time do
      databases.each do |db_name, db|
        one_time_sql_updates.each { |sql_file| run_sql( db,sql_file ) }
      end
    end

    desc "compare images for all sites on the current server" 
    task :assets do
      @default_image_folders = Dir.entries( File.join( SOURCE_PATH, 'files', 'img' )).select{ |filename| File.directory?( File.join( SOURCE_PATH, 'files', 'img', filename ) ) and filename[0,1] != '.' }
      default_images = Dir.entries( File.join( SOURCE_PATH, 'files', 'img' )).select{ |filename| filename =~ /\.(jpg|jpeg|gif|png)$/ }
      databases.each do |db_name, db|
        site_image_folder = File.join( (SITE_PATTERN % db_name), 'img' )
        next unless File.exists?( site_image_folder )
        site_images = Dir.entries( site_image_folder ).select{ |filename| filename =~ /\.(jpg|jpeg|gif|png)$/ }
        @missing_images = default_images - site_images
        next if @missing_images.empty?
        @missing_images.each do |img|
          log("Copying #{SOURCE_PATH}/files/img/#{img}\n")
          %x[ cp #{SOURCE_PATH}/files/img/#{img} #{site_image_folder} ]
        end

        @site_images_subfolders = Dir.entries( site_image_folder ).select{ |filename| File.directory?( File.join( site_image_folder, filename ) ) and filename[0,1] != '.' }
        @missing_folders = @default_image_folders - @site_images_subfolders
        next if @missing_folders.empty?
        @missing_folders.each do |folder|
          log("Copying #{SOURCE_PATH}/files/img/#{folder}\n")
          system "cp -r #{SOURCE_PATH}/files/img/#{folder} #{site_image_folder}"
        end
      end
    end


    def run_sql( db, sql_file )
      connection.check_sql_file( File.join( MIGRATIONS_PATH, sql_file )).each do |sql_statement|
        begin
          db.update sql_statement
        rescue Mysql::Error => e
          print db.name + " error: " + sql_statement
        end
      end
    end

    def sql_updates
      @updates ||= Dir.new( MIGRATIONS_PATH ).entries.select{ |f| Regexp.new( "^#{version}" ).match f }
    end
    def one_time_sql_updates
      @one_time_updates ||= Dir.new( MIGRATIONS_PATH ).entries.select{ |f| Regexp.new( "one_time_update_#{version}" ).match f }
    end
    def version
      version = ENV['VERSION'] || Dir.new( MIGRATIONS_PATH ).entries.select{ |f| f =~ /^\d\.\d{1,2}/ }.max[ /[\d\.]+/, 0 ]
      raise "indicate a version with amp:migrate:sql VERSION=3.x.x" if version.nil? 
      version
    end
    def databases
      dbs = single_database || connection.find
      log("\n\n#{Time.now} - VERSION: #{@version} DBS: #{( dbs.size == 1 ? dbs.keys.first : 'ALL')}\n" )
      dbs
    end
    def single_database
      return unless ENV['DB'] 
      db = connection.new( ENV['DB'] )
      raise "database #{db.name} is not a valid AMP database" unless db.valid?
      Hash[ db.name => db ]
    end
  end
end
