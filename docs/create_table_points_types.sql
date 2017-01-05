use Sprints_Points;

CREATE TABLE points_types(
	id INT NOT NULL auto_increment,
	name VARCHAR(20) NOT NULL,
	default_points INT NOT NULL,
	PRIMARY KEY(id)
)
ENGINE = InnoDB;
