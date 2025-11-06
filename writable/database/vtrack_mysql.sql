CREATE TABLE IF NOT EXISTS `homes` (

    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,

    address TEXT NOT NULL,

    road_id INTEGER NULL,

    sub_road_id INTEGER NULL,

    address_id INTEGER NULL,

    no_of_members INTEGER,

    has_assessment TEXT,

    assessment_number TEXT,

    resident_type TEXT,

    waste_disposal TEXT

);


CREATE TABLE IF NOT EXISTS `members` (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  home_id INT NOT NULL,
  full_name TEXT NOT NULL,
  name_with_initial TEXT NOT NULL,
  age INT NOT NULL,
  member_type ENUM('permanent', 'temporary') NOT NULL,
  nic VARCHAR(20),
  gender ENUM('male', 'female', 'other') NOT NULL,
  occupation TEXT NOT NULL,
  occupation_other TEXT,
  school TEXT,
  grade TEXT,
  university_name TEXT,
  disabled ENUM('yes', 'no') NOT NULL,
  land_house_status ENUM('plot', 'no_house', 'no_land_house') NOT NULL,
  whatsapp VARCHAR(20),
  cv TEXT,
  FOREIGN KEY(home_id) REFERENCES homes(id) ON DELETE CASCADE
);




CREATE TABLE users (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);

INSERT INTO users VALUES(1,'admin','admin123');

CREATE TABLE `migrations` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `version` VARCHAR(255) NOT NULL,
  `class` VARCHAR(255) NOT NULL,
  `group` VARCHAR(100) NOT NULL,
  `namespace` VARCHAR(255) NOT NULL,
  `time` INT NOT NULL,
  `batch` INT NOT NULL
);

INSERT INTO migrations VALUES(3,'2025-11-03-100901','App\Database\Migrations\CreateRoadsTables','App','App',1762146127,1);
INSERT INTO migrations VALUES(4,'2025-11-03-101000','App\Database\Migrations\AddLocationColumnsToHomes','default','App',1762147050,2);
CREATE TABLE `roads` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`name` VARCHAR(255) NOT NULL,
	`created_at` DATETIME NULL
);
INSERT INTO roads VALUES(1,'979 Main road','2025-11-03 06:09:08');
INSERT INTO roads VALUES(2,'979 Side road','2025-11-03 06:09:08');
INSERT INTO roads VALUES(3,'223 Main road','2025-11-03 06:09:08');
INSERT INTO roads VALUES(4,'223 Side road','2025-11-03 06:09:08');
INSERT INTO roads VALUES(5,'Korala maima main road','2025-11-03 06:09:08');
INSERT INTO roads VALUES(6,'Korala maima side road','2025-11-03 06:09:08');
INSERT INTO roads VALUES(7,'Maddegoda polhena main road','2025-11-03 06:09:08');
INSERT INTO roads VALUES(8,'Maddegoda polhena side road','2025-11-03 06:09:08');
INSERT INTO roads VALUES(9,'Praja mandala para main road','2025-11-03 06:09:08');
INSERT INTO roads VALUES(10,'Praja mandala para side road','2025-11-03 06:09:08');
INSERT INTO roads VALUES(11,'327 Main road','2025-11-03 06:09:08');
INSERT INTO roads VALUES(12,'327 Side road','2025-11-03 06:09:08');
CREATE TABLE `sub_roads` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`road_id` INTEGER NOT NULL,
	`name` VARCHAR(255) NOT NULL,
	`created_at` DATETIME NULL
);
INSERT INTO sub_roads VALUES(1,2,'979 1st lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(2,2,'979 2nd lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(3,2,'979 3rd lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(4,2,'Selinco Waththa','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(5,2,'979 4th lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(6,2,'Haritha uyana','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(7,2,'979 5th lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(8,2,'Sisla uyana','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(9,2,'Jaya mawatha','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(10,2,'979 6th Lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(11,2,'979 7th lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(12,2,'Seram lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(13,2,'979 8th Lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(14,2,'979 9th lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(15,2,'Golad lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(16,2,'pragathi mawatha','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(17,2,'Sisira mawatha','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(18,2,'Metro Niwas road','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(19,2,'Green Lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(20,2,'Ranawiru Chrandrakumara mawatha','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(21,2,'979 10th lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(22,2,'979 11 lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(23,4,'223 1st lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(24,4,'223 2nd lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(25,4,'223 3rd lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(26,4,'223 4th lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(27,4,'Gorak gaha handiya para','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(28,4,'Daham mawatha','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(29,4,'suhada mawatha II','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(30,4,'223 8th lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(31,4,'223 9th lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(32,4,'223 10th lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(33,4,'223 11 lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(34,6,'Welamada Para(Alubogahawaththa)','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(35,6,'Korala maima 1st lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(36,6,'Korala maima 2nd road','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(37,6,'Annasiwaththa para','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(38,6,'Pokuna para','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(39,6,'Moragahalandha para','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(40,6,'Korala maima 3rd lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(41,6,'Korala maima 4th lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(42,6,'rubber waththa para','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(43,6,'Mudhaleege para','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(44,8,'Ranawiru Kapila Bandara mawatha','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(45,8,'Maddegoda 1st lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(46,8,'Maddegoda 2nd lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(47,8,'Dewala para','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(48,8,'Alubogahawaththa para','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(49,8,'Dunkolamaduwa Para','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(50,8,'Maddegoda 3rd lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(51,8,'Maddegoda 4th lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(52,8,'Maddegoda 5th lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(53,8,'Maddegoda 6th lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(54,10,'Suhada Mawatha I','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(55,10,'Mangala mawatha','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(56,10,'prajamadala 2nd lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(57,12,'327 1st lane','2025-11-03 06:09:08');
INSERT INTO sub_roads VALUES(58,12,'Kurudugaha waththa para','2025-11-03 06:09:08');
CREATE TABLE `addresses` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`road_id` INTEGER NULL,
	`sub_road_id` INTEGER NULL,
	`address` VARCHAR(255) NOT NULL,
	`created_at` DATETIME NULL
);

DELETE FROM sqlite_sequence;
INSERT INTO sqlite_sequence VALUES('users',1);
INSERT INTO sqlite_sequence VALUES('member_offers',15);
INSERT INTO sqlite_sequence VALUES('members',11);
INSERT INTO sqlite_sequence VALUES('migrations',4);
INSERT INTO sqlite_sequence VALUES('roads',12);
INSERT INTO sqlite_sequence VALUES('sub_roads',58);
INSERT INTO sqlite_sequence VALUES('addresses',146);
CREATE INDEX `roads_name` ON `roads` (`name`);
CREATE INDEX `sub_roads_road_id` ON `sub_roads` (`road_id`);
CREATE INDEX `addresses_road_id` ON `addresses` (`road_id`);
CREATE INDEX `addresses_sub_road_id` ON `addresses` (`sub_road_id`);
COMMIT;
