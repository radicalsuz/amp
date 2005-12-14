
    -- This file was automatically generated from an XSL template, which is
    -- why it looks so ugly.  Editing it by hand would be a bad idea.
    --

    CREATE TABLE DB_TABLE_PREFIXItem (
  DB_COLUMN_PREFIXid
      INTEGER
    
      NOT NULL
    
      ,
    DB_COLUMN_PREFIXcanContainChildren
      SMALLINT
    
      NOT NULL
    
      ,
    DB_COLUMN_PREFIXdescription
      TEXT
    
      ,
    DB_COLUMN_PREFIXkeywords
      VARCHAR(
      
	  255
	
      )
    
      ,
    DB_COLUMN_PREFIXownerId
      INTEGER
    
      NOT NULL
    
      ,
    DB_COLUMN_PREFIXsummary
      VARCHAR(
      
	  255
	
      )
    
      ,
    DB_COLUMN_PREFIXtitle
      VARCHAR(
      
	  128
	
      )
    
      ,
    DB_COLUMN_PREFIXviewedSinceTimestamp
      INTEGER
    
      NOT NULL
    
      ,
    DB_COLUMN_PREFIXoriginationTimestamp
      INTEGER
    
      NOT NULL
    
    );

  
    CREATE INDEX DB_TABLE_PREFIXItem_99070
    ON DB_TABLE_PREFIXItem
    (DB_COLUMN_PREFIXkeywords);

  
    CREATE INDEX DB_TABLE_PREFIXItem_21573
    ON DB_TABLE_PREFIXItem
    (DB_COLUMN_PREFIXownerId);

  
    CREATE INDEX DB_TABLE_PREFIXItem_54147
    ON DB_TABLE_PREFIXItem
    (DB_COLUMN_PREFIXsummary);

  
    CREATE INDEX DB_TABLE_PREFIXItem_90059
    ON DB_TABLE_PREFIXItem
    (DB_COLUMN_PREFIXtitle);

  
    ALTER TABLE DB_TABLE_PREFIXItem
    ADD PRIMARY KEY (DB_COLUMN_PREFIXid);
    

    INSERT INTO DB_TABLE_PREFIXSchema (
      DB_COLUMN_PREFIXname,
      DB_COLUMN_PREFIXmajor,
      DB_COLUMN_PREFIXminor
      ) VALUES (
      'Item',
      1,
      1
      );

  