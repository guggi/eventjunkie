CREATE TABLE IF NOT EXISTS event (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    creationDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    name VARCHAR(50),
    address VARCHAR(50),
    latitude DECIMAL(17,14),
    longitude DECIMAL(17,14),
    eventDate TIMESTAMP,
    description VARCHAR(200),
    p
    CONSTRAINT pk_event PRIMARY KEY(id)
);

/*
CREATE TABLE IF NOT EXISTS user (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(50), NOT NULL
    CONSTRAINT pk_user PRIMARY KEY(id),
);*/

DROP TABLE event;

INSERT INTO event (name, address, latitude, longitude, eventDate, description) VALUES(
    'Event 1','Ort 1', 43.6, 32.3, '2008-03-02', 'Hihihi'
);

INSERT INTO event (name, address, latitude, longitude, eventDate, description) VALUES(
    'Event 2','Ort 2', 33.6, 42.3, '2008-03-02', 'Hihihi'
);

INSERT INTO event (name, address, latitude, longitude, eventDate, description) VALUES(
    'Event 3','Ort 3', 38.6, 42.3, '2008-03-02', 'Hihihi'
);