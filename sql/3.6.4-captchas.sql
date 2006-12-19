CREATE TABLE IF NOT EXISTS form_captchas ( 
    session varchar( 100 ) NOT NULL,
    captcha varchar( 20 ) NOT NULL,
    issued_at TIMESTAMP,
    PRIMARY KEY ( session )
) TYPE = MyISAM;
