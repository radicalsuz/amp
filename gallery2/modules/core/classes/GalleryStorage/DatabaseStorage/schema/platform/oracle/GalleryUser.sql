
    -- This file was automatically generated from an XSL template, which is
    -- why it looks so ugly.  Editing it by hand would be a bad idea.
    --

    CREATE TABLE DB_TABLE_PREFIXUser (
  DB_COLUMN_PREFIXid
      INTEGER
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXuserName
      VARCHAR2(
       32 
      )
    
    NOT NULL
  
      ,
    DB_COLUMN_PREFIXfullName
      VARCHAR2(
       128 
      )
    
      ,
    DB_COLUMN_PREFIXhashedPassword
      VARCHAR2(
       128 
      )
    
      ,
    DB_COLUMN_PREFIXemail
      VARCHAR2(
       128 
      )
    
      ,
    DB_COLUMN_PREFIXlanguage
      VARCHAR2(
       128 
      )
    
    );

  
    ALTER TABLE DB_TABLE_PREFIXUser
      ADD PRIMARY KEY (DB_COLUMN_PREFIXid)
  
      ADD UNIQUE (DB_COLUMN_PREFIXuserName)
  ;
  

    INSERT INTO DB_TABLE_PREFIXSchema (
      DB_COLUMN_PREFIXname,
      DB_COLUMN_PREFIXmajor,
      DB_COLUMN_PREFIXminor
      ) VALUES (
      'User',
      1,
      0
      );

  