update navtbl set `sql` =  Replace( `sql`,  "articles a, articletype t Left", "(articles a, articletype t) Left" ) where `sql` like "%articles a, articletype t Left%";
