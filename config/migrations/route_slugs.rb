require File.dirname(__FILE__) + '/../amp_databases'

namespace :amp do
  desc "create basic url slugs for articles"
  task :generate_slugs do
    sql = "insert into route_slugs (name, owner_type, owner_id) select lower( replace( replace( title, ' ', '-' ), '_', '-'  )) as slug, 'article' as owner_type, id from articles where !isnull(title) and title != '';"
    AmpDatabase.update_all sql

    sql = "insert into route_slugs (name, owner_type, owner_id) select lower( replace( replace( `type`, ' ', '-' ), '_', '-' )) as slug, 'section' as owner_type, id from articletype where !isnull(`type`) and `type` != '';"
    AmpDatabase.update_all sql
    
    # remove non-alpha-numeric characters from route slug
    AmpDatabase.find.each do |db_name, db|
      items = db.matches "select * from route_slugs where name REGEXP '[^-A-Z0-9a-z]'"
      items.each do |item|
        db.update "UPDATE route_slugs set name='#{item['name'].gsub(/[^-A-Z0-9a-z]/, '')}' where id=#{item['id']}"
      end
    end

    # de-dup route slugs with same name by appending a number to end of name
    AmpDatabase.find.each do |db_name, db|
      items = db.matches "select count(id) as quantity, name from route_slugs group by name having quantity > 1"
      items.each do |item|
        multi_name_record = db.matches "select * from route_slugs where name = '#{item['name']}'"
        multi_name_record.each_with_index do |record, i|
          next if i == 0
          db.update "update route_slugs set name='#{record['name'] + '-' + i.to_s}' where id = #{record['id']}"
        end
      end
    end
  end
end
