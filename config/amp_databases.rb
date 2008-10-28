#!/usr/bin/env ruby

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

  attr_accessor :name

  def initialize( name = nil )
    @name = name
  end

  def self.new_connection( db_name = nil )
	if !(@connection_warning || ENV['MYSQL_HOST'])
		puts "this script runs against localhost unless MYSQL_HOST is specified" 
	    @connection_warning = true	
	end
    @mysql_host ||= ENV['MYSQL_HOST'] || 'localhost'
    @mysql_user ||= ENV['MYSQL_USER'] || 'root'
    unless @mysql_pass
      unless @mysql_pass = ENV['MYSQL_PASS']
        print "\nmySQL password for #{@mysql_user}: "
        `stty -echo`
        ENV['MYSQL_PASS'] = @mysql_pass = STDIN.gets.chomp
        `stty echo`
      end
    end

    adapter = Mysql.init
    adapter.connect( @mysql_host, @mysql_user, @mysql_pass, db_name, 3306 )
    adapter
  end

  def self.find
    dbcon = new_connection
    database_result = dbcon.query "show databases"
    databases = database_result.all_hashes.map { |item| item["Database"] }
    amp_databases = databases.select { |database| valid? database } 
    Hash[ *amp_databases.map{ |database_name| [ database_name, AmpDatabase.new( database_name ) ] }.flatten ]
  end

  def self.valid?(db_name)
    dbcon = new_connection
    dbcon.query("use " + db_name)
    dbcon.query("show tables like 'articlereltype'").num_rows > 0
  end

  def valid?
    self.class.valid? name
  end

  def update(sql)
	dbcon = self.class.new_connection(@name)
    dbcon.query(sql)
    result = dbcon.affected_rows
	dbcon.close
	result
  end

  def matches(sql)
	dbcon = self.class.new_connection(@name)
    result = dbcon.query(sql)
	dbcon.close
    result.all_hashes 
  end

  def self.check_sql_file(filename)
    return [ filename ] unless File.exists? filename
    File.read(filename).split /;\n/
  end

  def self.update_all(sql)
    sql_statements = check_sql_file sql

    find.each do |db_name, db|
      sql_statements.each do |sql_statement|
        sql_desc = sql + ": " + sql_statement
        sql_desc = sql_statement unless sql != sql_statement

        begin
          affected = db.update sql_statement 
        rescue
          $stderr.print "#{db_name} update failed : #{sql_desc} \n!!  #{$!}\n\n"
          next
        end

        print "#{db_name} - #{affected} items affected : #{sql_desc}\n"
      end
    end
  end
end
