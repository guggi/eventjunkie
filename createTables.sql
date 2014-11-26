CREATE TABLE IF NOT EXISTS role (
    id INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    create_time TIMESTAMP,
    update_time TIMESTAMP,
    can_admin TINYINT DEFAULT 0 NOT NULL
);


CREATE TABLE IF NOT EXISTS user (
    id INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    role_id INT UNSIGNED NOT NULL,
    status TINYINT NOT NULL,
    email VARCHAR(255),
    new_email VARCHAR(255),
    username VARCHAR(255),
    password VARCHAR(255),
    auth_key VARCHAR(255),
    api_key VARCHAR(255),
    login_ip VARCHAR(45),
    login_time TIMESTAMP,
    create_ip VARCHAR(45),
    create_time TIMESTAMP,
    update_time TIMESTAMP,
    ban_time TIMESTAMP,
    ban_reason VARCHAR(255),
    FOREIGN KEY (role_id) REFERENCES role (id)
);
CREATE UNIQUE INDEX user_email ON user (email);
CREATE UNIQUE INDEX user_username ON user (username);
CREATE INDEX user_role_id ON user (role_id);

CREATE TABLE profile (
    id INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    create_time TIMESTAMP,
    update_time TIMESTAMP,
    full_name VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES user (id)
);
CREATE INDEX profile_user_id ON profile (user_id);

CREATE TABLE IF NOT EXISTS event (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL, /* geht bei mir nur unsigned */
    creation_date TIMESTAMP,
    name VARCHAR(50) NOT NULL,
    address VARCHAR(50) NOT NULL,
    latitude DECIMAL(17,14),
    longitude DECIMAL(17,14),
    start_date TIMESTAMP NOT NULL,
    end_date TIMESTAMP,
    image VARCHAR(100),
    clicks INT UNSIGNED DEFAULT 0,
    description VARCHAR(1000),
    note VARCHAR(1000),
    CONSTRAINT pk_event PRIMARY KEY(id),
    CONSTRAINT fk_event_user_id FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS socialmedia (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    event_id INT UNSIGNED NOT NULL,
    site_name VARCHAR(100),
    url VARCHAR(500) NOT NULL,
    CONSTRAINT pk_socialmedia PRIMARY KEY(id),
    CONSTRAINT fk_socialmedia_event_id FOREIGN KEY (event_id) REFERENCES event(id) ON DELETE CASCADE,
    CONSTRAINT uq_socialmedia UNIQUE(event_id, url)
);


DROP TABLE event;

INSERT INTO event (user_id, name, address, latitude, longitude, start_date, image, description) VALUES(
    1, 'DAS Event', 'Ort 1', 38.4, 32.3, '2015-05-02', 'http://ddragon.leagueoflegends.com/cdn/img/champion/splash/Jinx_0.jpg', 'Hihihi'
);

INSERT INTO event (name, address, latitude, longitude, eventDate, description) VALUES(
    'Event 2','Ort 2', 33.6, 42.3, '2008-03-02', 'Hihihi'
);

INSERT INTO event (name, address, latitude, longitude, eventDate, description) VALUES(
    'Event 3','Ort 3', 38.6, 42.3, '2008-03-02', 'Hihihi'
);
