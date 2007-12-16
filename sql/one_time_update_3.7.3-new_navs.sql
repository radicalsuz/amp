replace into badges ( id, publish, name, include, include_function ) values ( 10, 1, "Rating Update", "AMP/Badge/Rating.php", "amp_badge_rating" );
replace into navtbl( modid, name, titletext, include_file, include_function ) values ( 19, "Rate Articles", "Rate this Article", "AMP/Badge/Rating.php", "amp_badge_rating_block" );
replace into navtbl( modid, name, titletext, include_file, include_function, include_function_args ) values ( 19, "Articles By Author", "Articles By This Author", "AMP/Badge/ArticlesByAuthor.php", "amp_badge_articles_by_author", "limit=5&&morelink=list.php" );
replace into navtbl( id, modid, name, titletext, include_file, include_function, include_function_args ) values ( 140, 19, "Tag Cloud", "Popular Tags", "AMP/Badge/Tagcloud.php", "amp_badge_tag_cloud", "section=" );
replace into navtbl( modid, name, titletext, include_file, include_function, include_function_args ) values ( 19, "Blog Archives", "Archives", "AMP/Badge/ArticlesArchive.php", "amp_badge_articles_archive", "class=20" );
