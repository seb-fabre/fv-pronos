SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE IF NOT EXISTS `pr_day` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pr_season_id` int(10) unsigned NOT NULL,
  `number` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `day_FKIndex1` (`pr_season_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=73 ;

CREATE TABLE IF NOT EXISTS `pr_league` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) character set utf8 default NULL,
  `teams` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

CREATE TABLE IF NOT EXISTS `pr_match` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pr_day_id` int(10) unsigned NOT NULL,
  `pr_away_team_id` int(10) unsigned NOT NULL,
  `pr_home_team_id` int(10) unsigned NOT NULL,
  `home_goals` int(10) unsigned default NULL,
  `away_goals` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `match_FKIndex1` (`pr_home_team_id`),
  KEY `match_FKIndex2` (`pr_away_team_id`),
  KEY `match_FKIndex3` (`pr_day_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=695 ;

CREATE TABLE IF NOT EXISTS `pr_prono` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pr_match_id` int(10) unsigned NOT NULL,
  `pr_user_id` int(10) unsigned NOT NULL,
  `home_goals` int(10) unsigned default NULL,
  `away_goals` int(10) unsigned default NULL,
  `updated_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `prono_FKIndex1` (`pr_user_id`),
  KEY `prono_FKIndex2` (`pr_match_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16742 ;

CREATE TABLE IF NOT EXISTS `pr_season` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pr_league_id` int(10) unsigned NOT NULL,
  `label` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `pr_league_id` (`pr_league_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

CREATE TABLE IF NOT EXISTS `pr_season_teams` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pr_team_id` int(10) unsigned NOT NULL,
  `pr_season_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `pr_season_id` (`pr_season_id`),
  KEY `pr_team_id` (`pr_team_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=44 ;

CREATE TABLE IF NOT EXISTS `pr_team` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) character set utf8 default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=38 ;

CREATE TABLE IF NOT EXISTS `pr_user` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) character set utf8 default NULL,
  `passwd` varchar(40) character set utf8 default NULL,
  `email` varchar(100) character set utf8 default NULL,
  `pr_team_id` int(11) default NULL,
  `role` VARCHAR(10) NOT NULL DEFAULT "user",
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=45;

