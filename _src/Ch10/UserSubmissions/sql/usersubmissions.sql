
CREATE TABLE dinop_usersubmissions (
  id int(11) NOT NULL auto_increment,
  data text  NOT NULL,
  parent_id int(11) NOT NULL default '0',
  type varchar(36)  NOT NULL default '1',
  user_id int(11) NOT NULL default '0',
  date timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  title varchar(255)  NOT NULL default '',
  user_name varchar(250)  NOT NULL default '',
  PRIMARY KEY  (id)
) COMMENT='Table for holding user submitted content' ;

