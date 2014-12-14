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
    ban_time TIMESTAMP NULL DEFAULT NULL,
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

CREATE TABLE user_key (
    id INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    type TINYINT NOT NULL,
    `key` VARCHAR(255) NOT NULL,
    create_time TIMESTAMP,
    consume_time TIMESTAMP,
    expire_time TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user (id)
);
CREATE UNIQUE INDEX user_key_key ON user_key (`key`);
CREATE INDEX user_key_user_id ON user_key (user_id);

CREATE TABLE IF NOT EXISTS event (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL, /* geht bei mir nur unsigned */
    creation_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
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

INSERT INTO role (id, name, create_time, update_time, can_admin) VALUES (1, 'Admin', '2014-10-26 21:39:10', null, 1);
INSERT INTO role (id, name, create_time, update_time, can_admin) VALUES (2, 'User', '2014-10-26 21:39:10', null, 0);

INSERT INTO user (id, role_id, status, email, new_email, username, password, auth_key, api_key, login_ip, login_time, create_ip, create_time, update_time, ban_time, ban_reason) VALUES (1, 1, 1, 'neo@neo.com', null, 'neo', '$2y$13$TwrvoO6XDvaHBSzDp4Jl0.GSj5ULQ21IjSs0vr4zx2s9MrRDT83Ga', 'VgcR3_Uch3jNMr5MDiltEoKeGtlrWd34', 'qr5hMoPOHOR3_6a4PYM9mScs9lrxWkHL', '127.0.0.1', '2014-12-10 18:56:26', null, '2014-10-26 21:39:10', '2014-10-31 19:05:35', null, null);

DROP TABLE event;
