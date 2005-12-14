

    UPDATE DB_TABLE_PREFIXSchema 
      SET DB_COLUMN_PREFIXmajor=1,
          DB_COLUMN_PREFIXminor=1
      WHERE DB_COLUMN_PREFIXname='ThumbnailImage' AND
          DB_COLUMN_PREFIXmajor=1 AND
          DB_COLUMN_PREFIXminor=0;

  