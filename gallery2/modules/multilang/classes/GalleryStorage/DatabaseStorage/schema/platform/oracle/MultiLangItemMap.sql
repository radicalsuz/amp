
    -- This file was automatically generated from an XSL template, which is
    -- why it looks so ugly.  Editing it by hand would be a bad idea.
    --

    CREATE TABLE DB_TABLE_PREFIXMultiLangItemMap (
  DB_COLUMN_PREFIXitemId
      INTEGER
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXlanguage
      VARCHAR2(
       32 
      )
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXtitle
      VARCHAR2(
       128 
      )
    
      ,
    DB_COLUMN_PREFIXsummary
      VARCHAR2(
       255 
      )
    
      ,
    DB_COLUMN_PREFIXdescription
      VARCHAR2(4000)
    
    );

  
    ALTER TABLE DB_TABLE_PREFIXMultiLangItemMap
      ADD PRIMARY KEY (DB_COLUMN_PREFIXitemId
        ,
      DB_COLUMN_PREFIXlanguage)
  ;
  

    INSERT INTO DB_TABLE_PREFIXSchema (
      DB_COLUMN_PREFIXname,
      DB_COLUMN_PREFIXmajor,
      DB_COLUMN_PREFIXminor
      ) VALUES (
      'MultiLangItemMap',
      1,
      0
      );

  