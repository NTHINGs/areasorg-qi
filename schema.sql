-- ****************** AREASORG QI ******************;
-- ***************************************************;
-- ************************************** %TABLE_PREFIX%areas

CREATE TABLE %TABLE_PREFIX%areas
(
 id               INT NOT NULL AUTO_INCREMENT ,
 nombre           VARCHAR(100) NOT NULL ,
 organizacion     VARCHAR(64) NOT NULL,
 
PRIMARY KEY (id)
)%CHARSET_COLLATE%;