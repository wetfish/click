-- click's tables. wetfish/server runs this once, as click's own database user,
-- when a deploy first creates click's database (see setup.conf). The dev
-- database in docker-compose.override.yml runs it when it starts with an empty
-- volume. It never runs against existing data, so a change here needs a
-- migration in migrations/ for databases that already exist.

CREATE TABLE `Pages` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Page` varchar(255) NOT NULL,
  `Time` int(10) NOT NULL,
  `IP` varchar(45) NOT NULL,
  `Data` longtext NOT NULL,
  `Deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `Deleted` (`Deleted`),
  KEY `Page` (`Page`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_as_ci;

-- the 50 longest posts, named as in older servers' databases
CREATE VIEW `BiggestPages` AS
  SELECT `ID`, `Page`, `Time`, `IP`, `Data`, `Deleted`, LENGTH(`Data`) AS `LENGTH(``Data``)`
  FROM `Pages`
  WHERE `Deleted` = '0'
  ORDER BY LENGTH(`Data`) DESC
  LIMIT 50;
