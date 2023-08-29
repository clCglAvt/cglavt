-- ===========================================================================
-- Copyright (C) 2001-2003 Rodolphe Quiedeville <rodolphe@quiedeville.org>
-- Copyright (C) 2007      Laurent Destailleur  <eldy@users.sourceforge.net>
-- Copyright (C) 2007-2012 Regis Houssin        <regis.houssin@capnetworks.com>
-- Copyright (C) 2010      Juanjo Menent        <jmenent@2byte.es>
-- MOD CCA 26/12/2014 Creation table Pour Suivi Client 
-- This program is free software; you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation; either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program. If not, see <http://www.gnu.org/licenses/>.
--
-- ===========================================================================

CREATE TABLE llx_cglavt_tiers_suivi (
`rowid` INT NOT NULL AUTO_INCREMENT ,
`datec` DATETIME NOT NULL ,
`tms` DATETIME  , 
`entity` INT NOT NULL DEFAULT '1' ,
`fk_user_create` INT NOT NULL ,
`fk_user_mod` INT ,
`fk_soc` INT ,
`fk_socpeople` INT ,
`description` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`urgence` SMALLINT  DEFAULT '0' ,
`date_action` DATETIME ,
`action_realisee` SMALLINT DEFAULT '0' ,
PRIMARY KEY ( `rowid` )
) ENGINE = InnoDB COMMENT = 'Permet de faire le suivi des clients';