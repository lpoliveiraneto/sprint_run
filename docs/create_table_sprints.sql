use Sprints_Points;

CREATE TABLE sprints(
	id INT NOT NULL,
	name VARCHAR(20) NOT NULL,
	obs VARCHAR(100) NOT NULL,
	PRIMARY KEY(id)
)
ENGINE = InnoDB;
