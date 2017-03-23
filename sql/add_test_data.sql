-- Lisää INSERT INTO lauseet tähän tiedostoon
INSERT INTO Users (username, realname, password, administrator, registration_date, last_seen) VALUES (
    'admin', 'admin', '123456', TRUE, NOW(), NOW()
), (
    'kayttaja', 'norsu', '654321', FALSE, NOW(), NOW()
);

INSERT INTO Messages (userid, text, sent) VALUES (
    1, 'morjens morjens #testi', NOW()
), (
    2, 'hirveetä kattoo #testi #toinentagi', NOW()
);

INSERT INTO Tags (text) VALUES (
    'testi'
), (
    'toinentagi'
);

INSERT INTO Tagged (messageid, tagid) VALUES (
    1, 1
), (
    2, 1
), (
    2, 2
);
