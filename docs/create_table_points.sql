use Sprints_Points

CREATE TABLE points(
	id INT NOT NULL auto_increment,
	sprint_id INT NOT NULL,
	user_id INT NOT NULL,
	point_type_id INT NOT NULL,
	obs VARCHAR(100),
	PRIMARY KEY(id),
	FOREIGN KEY(sprint_id) REFERENCES sprints(id),
	FOREIGN KEY(user_id) REFERENCES usuarios(id),
	FOREIGN KEY(point_type_id) REFERENCES points_types(id)
)
ENGINE = InnoDB;
