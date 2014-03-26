-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the TYPOlight *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************

-- 
-- Table `tl_page`
-- 

CREATE TABLE `tl_page` (

  `cca_rr_root` int(10) unsigned NOT NULL default '0',

  KEY `cca_rr_root_ix` (`cca_rr_root`),

) ENGINE=MyISAM DEFAULT CHARSET=utf8;
