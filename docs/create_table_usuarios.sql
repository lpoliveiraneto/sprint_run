use Sprints_Points

CREATE TABLE usuarios(
	id INT NOT NULL auto_increment,
	name VARCHAR(20) NOT NULL,
	passwd VARCHAR(20) NOT NULL,
	email VARCHAR(20) NOT NULL,
	PRIMARY KEY(id)	
)
ENGINE = InnoDB;
