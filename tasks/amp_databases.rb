#!/usr/bin/env ruby

AMP_INSTALL_DIR='/home/amp/'
MYSQL_HOST='localhost'
MYSQL_USER='admin'
MYSQL_PASS='changeme'

require "rubygems"
require "mysql"
class Mysql::Result
  def all_hashes
    rows = []
    all_fields = fetch_fields.inject({}) { |fields, f| fields[f.name] = nil; fields }
    each_hash { |row| rows << all_fields.dup.update(row) }
    rows
  end
end

class AmpDatabase
	attr_reader :name

	def initialize( name, host = MYSQL_HOST )
		self.name=name
		@host=host
		@dbcon = Mysql.init
	end

	def connect
		@dbcon.connect( @host, MYSQL_USER, MYSQL_PASS, @name, 3306 )
		result = yield @dbcon
		@dbcon.close
		result
	end

	def name=(name)
		@name = name
	end

	def self.find
		dbcon = Mysql.init
		amp_dbs = []
		[ MYSQL_HOST ].each do |current_host|
			dbcon.connect( current_host, MYSQL_USER, MYSQL_PASS, nil, 3306 )
			database_result = dbcon.query "show databases"
			databases = database_result.all_hashes.map { |item| item["Database"] }
			amp_databases = databases.find_all do |database| 
				dbcon.query("use " + database)
				dbcon.query("show tables like 'articlereltype'").num_rows > 0
			end
			amp_dbs += amp_databases.map{|database_name| AmpDatabase.new( database_name, current_host )}
			dbcon.close
		end
		amp_dbs.sort { |x,y| x.name <=> y.name }
	end

	def update(sql)
		connect do |dbcon|
			dbcon.query(sql)
			dbcon.affected_rows
		end
	end
	def matches(sql)
		connect do |dbcon| 
			result = dbcon.query sql
			result.all_hashes	
		end
	end

	def self.check_sql_file(filename)
		return [ filename ] unless File.exists? filename
		File.read(filename).split /;\n/
	end

	def self.update_all(sql)
		sql_statements = check_sql_file sql

		find.each do |db|
			sql_statements.each do |sql_statement|
				sql_desc = sql + ": " + sql_statement
				sql_desc = sql_statement unless sql != sql_statement

				begin
					affected = db.connect do |dbcon |
						dbcon.query sql_statement 
						dbcon.affected_rows
					end
				rescue
					$stderr.print "#{db.name} update failed : #{sql_desc} \n!!  #{$!}\n\n"
					next
				end

				print "#{db.name} - #{affected} items affected : #{sql_desc}\n"
			end
		end
	end
end

action = ARGV[0]
sql = ARGV[1]
#
#DATABASE LIST
if action == 'list'
	print AmpDatabase.find.map{ |db| db.name}.join("\n") + "\n"
end

#DATABASE UPDATE 
if action == 'update' && sql
	AmpDatabase.update_all sql
end

#DATABASE SEARCH 
if action == 'search' && sql
	require 'pp'
	sql_statements = AmpDatabase.check_sql_file sql
	pp sql_statements
	AmpDatabase.find.each do |db|
		sql_statements.each do |sql_statement|
			sql_desc = sql + ": " + sql_statement
			sql_desc = sql_statement unless sql != sql_statement
			begin
				result = db.matches sql_statement
				next if result.size == 0
			rescue
				$stderr.print "#{db.name} search failed : #{sql_desc} \n!!  #{$!}\n\n"
				next
			end

			print "\n\n#{db.name} - #{result.size} items found : #{sql_desc}\n"
			pp result
		end 
	end
end

#DATABASE DEPLOY
if action == 'deploy' && sql
	build_id = sql
	Dir.entries( AMP_INSTALL_DIR + "sql/" ).each do |filename|
		next unless Regexp.new( '^' + build_id + '-\w+\.sql$') =~ filename
		AmpDatabase.update_all( AMP_INSTALL_DIR + 'sql/' + filename )
	end
	#{build_id}-*.sql"
end

#DATABASE DEPLOY
if action == 'deploy_once' && sql
	build_id = sql
	Dir.entries( AMP_INSTALL_DIR + "sql/" ).each do |filename|
		next unless Regexp.new( '^one_time_update.' + build_id + '-\w+\.sql$') =~ filename
		AmpDatabase.update_all( AMP_INSTALL_DIR + 'sql/' + filename )
	end
end
