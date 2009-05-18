require File.dirname(__FILE__) + '/../amp_databases'

namespace :amp do
  namespace :pretty_urls do
	desc "create basic url slugs for articles and sections"
	task :generate do
	  raise "You must specify a database to work on: DB=database_name" unless ENV['DB']
	  db = AmpDatabase.new( ENV['DB'] );
	  sql = "insert into route_slugs (name, owner_type, owner_id) select lower( replace( replace( title, ' ', '-' ), '_', '-'  )) as slug, 'article' as owner_type, id from articles where !isnull(title) and title != '';"
	  db.update sql

	  sql = "insert into route_slugs (name, owner_type, owner_id) select lower( replace( replace( `type`, ' ', '-' ), '_', '-' )) as slug, 'section' as owner_type, id from articletype where !isnull(`type`) and `type` != '';"
	  db.update sql
	  
	  # remove non-alpha-numeric characters from route slug
		items = db.matches "select * from route_slugs where name REGEXP '[^-A-Z0-9a-z]'"
		items.each do |item|
		  db.update "UPDATE route_slugs set name='#{item['name'].gsub(/[^-A-Z0-9a-z]/, '')}' where id=#{item['id']}"
		end

	  # de-dup route slugs with same name by appending a number to end of name
		items = db.matches "select count(id) as quantity, name from route_slugs group by name having quantity > 1"
		items.each do |item|
		  multi_name_record = db.matches "select * from route_slugs where name = '#{item['name']}'"
		  multi_name_record.each_with_index do |record, i|
			next if i == 0
			db.update "update route_slugs set name='#{record['name'] + '-' + i.to_s}' where id = #{record['id']}"
		  end
		end
	end
	desc "drop existing pretty urls"
	task :clear do
	  raise "You must specify a database to work on: DB=database_name" unless ENV['DB']
	  db = AmpDatabase.new( ENV['DB'] );
	  sql = "delete from route_slugs" 
	  db.update sql
	end
  end
end
