
      ALTER TABLE DB_TABLE_PREFIXWatermarkImage
      DROP KEY DB_COLUMN_PREFIXname
      ;
    

    UPDATE DB_TABLE_PREFIXSchema 
      SET DB_COLUMN_PREFIXmajor=1,
          DB_COLUMN_PREFIXminor=1
      WHERE DB_COLUMN_PREFIXname='WatermarkImage' AND
          DB_COLUMN_PREFIXmajor=1 AND
          DB_COLUMN_PREFIXminor=0;

  