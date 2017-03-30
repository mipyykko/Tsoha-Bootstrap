-- Lisää CREATE TABLE lauseet tähän tiedostoon
CREATE TABLE Users(
    id SERIAL PRIMARY KEY,
    username varchar(32) UNIQUE NOT NULL,
    realname varchar(64) NOT NULL,
    password varchar(32) NOT NULL,
    description varchar(256),
    email varchar(64),
    administrator boolean NOT NULL DEFAULT FALSE,
    public_profile boolean NOT NULL DEFAULT TRUE,
    registration_date timestamp NOT NULL,
    last_seen timestamp NOT NULL
);

CREATE TABLE Messages(
    id SERIAL PRIMARY KEY,
    userid INTEGER REFERENCES Users(id),
    replyid INTEGER REFERENCES Messages(id),
    text varchar(255) NOT NULL,
    sent timestamp NOT NULL,
    public_message boolean NOT NULL DEFAULT TRUE
);

CREATE TABLE Tags(
    text varchar(64) UNIQUE NOT NULL PRIMARY KEY,
    id SERIAL
);

CREATE TABLE Tagged(
    messageid INTEGER REFERENCES Messages(id),
    tagid INTEGER REFERENCES Tags(id)
);

CREATE TABLE Followed(
    userid INTEGER REFERENCES Users(id),
    followed_userid INTEGER REFERENCES Users(id)
);