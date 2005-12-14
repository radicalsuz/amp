
    -- This file was automatically generated from an XSL template, which is
    -- why it looks so ugly.  Editing it by hand would be a bad idea.
    --

    CREATE TABLE DB_TABLE_PREFIXSchema (
  DB_COLUMN_PREFIXname
      VARCHAR(
      
	  128
	
      )
    
      NOT NULL
    
      ,
    DB_COLUMN_PREFIXmajor
      INTEGER
    
      NOT NULL
    
      ,
    DB_COLUMN_PREFIXminor
      INTEGER
    
      NOT NULL
    
    );

  
    ALTER TABLE DB_TABLE_PREFIXSchema
    ADD PRIMARY KEY (DB_COLUMN_PREFIXname);
    

    INSERT INTO DB_TABLE_PREFIXSchema (
      DB_COLUMN_PREFIXname,
      DB_COLUMN_PREFIXmajor,
      DB_COLUMN_PREFIXminor
      ) VALUES (
      'Schema',
      1,
      0
      );

  