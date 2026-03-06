-- SQL Script to insert users from Userlist.csv
-- Generated on 2025-04-03 16:05:13
-- Sets role_id to 7 for all users, leaves password empty, and sets auth_method to LDAP
-- Checks if users exist via email or login fields (to avoid duplicate key errors)

-- Lupia Matta
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Lupia.Matta', 
    '', 
    7, 
    'Lupia.Matta@nmbtz.com', 
    'Lupia', 
    'Matta', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Lupia.Matta@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Lupia.Matta@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Lupia.Matta@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Lupia.Matta')
);

-- Thomas Lyimo
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Thomas.Lyimo', 
    '', 
    7, 
    'Thomas.Lyimo@nmbtz.com', 
    'Thomas', 
    'Lyimo', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Thomas.Lyimo@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Thomas.Lyimo@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Thomas.Lyimo@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Thomas.Lyimo')
);

-- Elvida Kapya
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Elvida.Kapya', 
    '', 
    7, 
    'Elvida.Kapya@nmbtz.com', 
    'Elvida', 
    'Kapya', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Elvida.Kapya@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Elvida.Kapya@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Elvida.Kapya@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Elvida.Kapya')
);

-- Komugasho Baguma
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Komugasho.Baguma', 
    '', 
    7, 
    'Komugasho.Baguma@nmbtz.com', 
    'Komugasho', 
    'Baguma', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Komugasho.Baguma@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Komugasho.Baguma@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Komugasho.Baguma@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Komugasho.Baguma')
);

-- Nancy Filipo
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Nancy.Filipo', 
    '', 
    7, 
    'Nancy.Filipo@nmbtz.com', 
    'Nancy', 
    'Filipo', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Nancy.Filipo@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Nancy.Filipo@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Nancy.Filipo@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Nancy.Filipo')
);

-- Janet Rwegasila
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Janet.Rwegasila', 
    '', 
    7, 
    'Janet.Rwegasila@nmbtz.com', 
    'Janet', 
    'Rwegasila', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Janet.Rwegasila@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Janet.Rwegasila@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Janet.Rwegasila@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Janet.Rwegasila')
);

-- Janeth Buretta
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Janeth.Buretta', 
    '', 
    7, 
    'Janeth.Buretta@nmbtz.com', 
    'Janeth', 
    'Buretta', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Janeth.Buretta@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Janeth.Buretta@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Janeth.Buretta@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Janeth.Buretta')
);

-- Fred Kilimba
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Fred.Kilimba', 
    '', 
    7, 
    'Fred.Kilimba@nmbtz.com', 
    'Fred', 
    'Kilimba', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Fred.Kilimba@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Fred.Kilimba@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Fred.Kilimba@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Fred.Kilimba')
);

-- David Gwadenga
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'David.Gwadenga', 
    '', 
    7, 
    'David.Gwadenga@nmbtz.com', 
    'David', 
    'Gwadenga', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('David.Gwadenga@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('David.Gwadenga@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('David.Gwadenga@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('David.Gwadenga')
);

-- Victor Lema
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Victor.Lema', 
    '', 
    7, 
    'Victor.Lema@nmbtz.com', 
    'Victor', 
    'Lema', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Victor.Lema@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Victor.Lema@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Victor.Lema@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Victor.Lema')
);

-- Stella Motto
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Stella.Motto', 
    '', 
    7, 
    'Stella.Motto@nmbtz.com', 
    'Stella', 
    'Motto', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Stella.Motto@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Stella.Motto@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Stella.Motto@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Stella.Motto')
);

-- Patrick Makungu
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Patrick.Makungu', 
    '', 
    7, 
    'Patrick.Makungu@nmbtz.com', 
    'Patrick', 
    'Makungu', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Patrick.Makungu@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Patrick.Makungu@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Patrick.Makungu@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Patrick.Makungu')
);

-- Banza Timoth
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Banza.Timoth', 
    '', 
    7, 
    'Banza.Timoth@nmbtz.com', 
    'Banza', 
    'Timoth', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Banza.Timoth@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Banza.Timoth@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Banza.Timoth@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Banza.Timoth')
);

-- Neema Kassim
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Neema.Kassim', 
    '', 
    7, 
    'Neema.Kassim@nmbtz.com', 
    'Neema', 
    'Kassim', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Neema.Kassim@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Neema.Kassim@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Neema.Kassim@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Neema.Kassim')
);

-- Charles Nyamboha
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Charles.Nyamboha', 
    '', 
    7, 
    'Charles.Nyamboha@nmbtz.com', 
    'Charles', 
    'Nyamboha', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Charles.Nyamboha@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Charles.Nyamboha@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Charles.Nyamboha@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Charles.Nyamboha')
);

-- Hellen Allan
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Hellen.Allan', 
    '', 
    7, 
    'Hellen.Allan@nmbtz.com', 
    'Hellen', 
    'Allan', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Hellen.Allan@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Hellen.Allan@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Hellen.Allan@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Hellen.Allan')
);

-- Ntinginya Musisa
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Ntinginya.Musisa', 
    '', 
    7, 
    'Ntinginya.Musisa@nmbtz.com', 
    'Ntinginya', 
    'Musisa', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Ntinginya.Musisa@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Ntinginya.Musisa@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Ntinginya.Musisa@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Ntinginya.Musisa')
);

-- Bosco Mtiga
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Bosco.Mtiga', 
    '', 
    7, 
    'Bosco.Mtiga@nmbtz.com', 
    'Bosco', 
    'Mtiga', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Bosco.Mtiga@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Bosco.Mtiga@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Bosco.Mtiga@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Bosco.Mtiga')
);

-- Huruma Stewart
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Huruma.Stewart', 
    '', 
    7, 
    'Huruma.Stewart@nmbtz.com', 
    'Huruma', 
    'Stewart', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Huruma.Stewart@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Huruma.Stewart@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Huruma.Stewart@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Huruma.Stewart')
);

-- Victor Mpendazoe
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Victor.Mpendazoe', 
    '', 
    7, 
    'Victor.Mpendazoe@nmbtz.com', 
    'Victor', 
    'Mpendazoe', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Victor.Mpendazoe@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Victor.Mpendazoe@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Victor.Mpendazoe@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Victor.Mpendazoe')
);

-- Said Sungura
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Said.Sungura', 
    '', 
    7, 
    'Said.Sungura@nmbtz.com', 
    'Said', 
    'Sungura', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Said.Sungura@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Said.Sungura@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Said.Sungura@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Said.Sungura')
);

-- Emmanuel Nyimbo
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Emmanuel.Nyimbo', 
    '', 
    7, 
    'Emmanuel.Nyimbo@nmbtz.com', 
    'Emmanuel', 
    'Nyimbo', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Emmanuel.Nyimbo@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Emmanuel.Nyimbo@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Emmanuel.Nyimbo@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Emmanuel.Nyimbo')
);

-- Rington Kaizilege
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Rington.Kaizilege', 
    '', 
    7, 
    'Rington.Kaizilege@nmbtz.com', 
    'Rington', 
    'Kaizilege', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Rington.Kaizilege@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Rington.Kaizilege@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Rington.Kaizilege@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Rington.Kaizilege')
);

-- Charity Mwakalonge
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Charity.Mwakalonge', 
    '', 
    7, 
    'Charity.Mwakalonge@nmbtz.com', 
    'Charity', 
    'Mwakalonge', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Charity.Mwakalonge@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Charity.Mwakalonge@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Charity.Mwakalonge@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Charity.Mwakalonge')
);

-- Hassan Omary
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Hassan.Omary', 
    '', 
    7, 
    'Hassan.Omary@nmbtz.com', 
    'Hassan', 
    'Omary', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Hassan.Omary@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Hassan.Omary@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Hassan.Omary@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Hassan.Omary')
);

-- Irene Lyimo
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Irene.Lyimo', 
    '', 
    7, 
    'Irene.Lyimo@nmbtz.com', 
    'Irene', 
    'Lyimo', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Irene.Lyimo@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Irene.Lyimo@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Irene.Lyimo@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Irene.Lyimo')
);

-- Mgayo Juakali
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Mgayo.Juakali', 
    '', 
    7, 
    'Mgayo.Juakali@nmbtz.com', 
    'Mgayo', 
    'Juakali', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Mgayo.Juakali@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Mgayo.Juakali@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Mgayo.Juakali@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Mgayo.Juakali')
);

-- Mwamini Nassoro
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Mwamini.Nassoro', 
    '', 
    7, 
    'Mwamini.Nassoro@nmbtz.com', 
    'Mwamini', 
    'Nassoro', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Mwamini.Nassoro@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Mwamini.Nassoro@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Mwamini.Nassoro@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Mwamini.Nassoro')
);

-- Julieth Makalwe
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Julieth.Makalwe', 
    '', 
    7, 
    'Julieth.Makalwe@nmbtz.com', 
    'Julieth', 
    'Makalwe', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Julieth.Makalwe@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Julieth.Makalwe@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Julieth.Makalwe@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Julieth.Makalwe')
);

-- Baby Njella
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Baby.Njella', 
    '', 
    7, 
    'Baby.Njella@nmbtz.com', 
    'Baby', 
    'Njella', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Baby.Njella@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Baby.Njella@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Baby.Njella@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Baby.Njella')
);

-- Eric Moye
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Eric.Moye', 
    '', 
    7, 
    'Eric.Moye@nmbtz.com', 
    'Eric', 
    'Moye', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Eric.Moye@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Eric.Moye@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Eric.Moye@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Eric.Moye')
);

-- Abella Tarimo
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Abella.Tarimo', 
    '', 
    7, 
    'Abella.Tarimo@nmbtz.com', 
    'Abella', 
    'Tarimo', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Abella.Tarimo@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Abella.Tarimo@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Abella.Tarimo@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Abella.Tarimo')
);

-- Rachel Tsingay
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Rachel.Tsingay', 
    '', 
    7, 
    'Rachel.Tsingay@nmbtz.com', 
    'Rachel', 
    'Tsingay', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Rachel.Tsingay@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Rachel.Tsingay@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Rachel.Tsingay@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Rachel.Tsingay')
);

-- Upendo Mbajo
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Upendo.Mbajo', 
    '', 
    7, 
    'Upendo.Mbajo@nmbtz.com', 
    'Upendo', 
    'Mbajo', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Upendo.Mbajo@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Upendo.Mbajo@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Upendo.Mbajo@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Upendo.Mbajo')
);

-- Shaban Rajab
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Shaban.Rajab', 
    '', 
    7, 
    'Shaban.Rajab@nmbtz.com', 
    'Shaban', 
    'Rajab', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Shaban.Rajab@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Shaban.Rajab@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Shaban.Rajab@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Shaban.Rajab')
);

-- Joas Maheta
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Joas.Maheta', 
    '', 
    7, 
    'Joas.Maheta@nmbtz.com', 
    'Joas', 
    'Maheta', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Joas.Maheta@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Joas.Maheta@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Joas.Maheta@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Joas.Maheta')
);

-- Abeid Ngwanakilala
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Abeid.Ngwanakilala', 
    '', 
    7, 
    'Abeid.Ngwanakilala@nmbtz.com', 
    'Abeid', 
    'Ngwanakilala', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Abeid.Ngwanakilala@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Abeid.Ngwanakilala@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Abeid.Ngwanakilala@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Abeid.Ngwanakilala')
);

-- Ahmed Ubwa
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Ahmed.Ubwa', 
    '', 
    7, 
    'Ahmed.Ubwa@nmbtz.com', 
    'Ahmed', 
    'Ubwa', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Ahmed.Ubwa@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Ahmed.Ubwa@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Ahmed.Ubwa@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Ahmed.Ubwa')
);

-- Innocent Njela
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Innocent.Njela', 
    '', 
    7, 
    'Innocent.Njela@nmbtz.com', 
    'Innocent', 
    'Njela', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Innocent.Njela@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Innocent.Njela@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Innocent.Njela@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Innocent.Njela')
);

-- Siaga Mgweno
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Siaga.Mgweno', 
    '', 
    7, 
    'Siaga.Mgweno@nmbtz.com', 
    'Siaga', 
    'Mgweno', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Siaga.Mgweno@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Siaga.Mgweno@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Siaga.Mgweno@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Siaga.Mgweno')
);

-- Adelimu Mrosso
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Adelimu.Mrosso', 
    '', 
    7, 
    'Adelimu.Mrosso@nmbtz.com', 
    'Adelimu', 
    'Mrosso', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Adelimu.Mrosso@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Adelimu.Mrosso@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Adelimu.Mrosso@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Adelimu.Mrosso')
);

-- Kunegunda Dedede
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Kunegunda.Dedede', 
    '', 
    7, 
    'Kunegunda.Dedede@nmbtz.com', 
    'Kunegunda', 
    'Dedede', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Kunegunda.Dedede@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Kunegunda.Dedede@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Kunegunda.Dedede@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Kunegunda.Dedede')
);

-- Sebastian Kavishe
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Sebastian.Kavishe', 
    '', 
    7, 
    'Sebastian.Kavishe@nmbtz.com', 
    'Sebastian', 
    'Kavishe', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Sebastian.Kavishe@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Sebastian.Kavishe@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Sebastian.Kavishe@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Sebastian.Kavishe')
);

-- Kiyao Mwihava
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Kiyao.Mwihava', 
    '', 
    7, 
    'Kiyao.Mwihava@nmbtz.com', 
    'Kiyao', 
    'Mwihava', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Kiyao.Mwihava@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Kiyao.Mwihava@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Kiyao.Mwihava@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Kiyao.Mwihava')
);

-- Raphael Likaganga
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Raphael.Likaganga', 
    '', 
    7, 
    'Raphael.Likaganga@nmbtz.com', 
    'Raphael', 
    'Likaganga', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Raphael.Likaganga@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Raphael.Likaganga@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Raphael.Likaganga@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Raphael.Likaganga')
);

-- Jacquiline Mollel
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Jacquiline.Mollel', 
    '', 
    7, 
    'Jacquiline.Mollel@nmbtz.com', 
    'Jacquiline', 
    'Mollel', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Jacquiline.Mollel@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Jacquiline.Mollel@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Jacquiline.Mollel@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Jacquiline.Mollel')
);

-- Philipo Ndaro
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Philipo.Ndaro', 
    '', 
    7, 
    'Philipo.Ndaro@nmbtz.com', 
    'Philipo', 
    'Ndaro', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Philipo.Ndaro@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Philipo.Ndaro@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Philipo.Ndaro@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Philipo.Ndaro')
);

-- Kizito Kyando
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Kizito.Kyando', 
    '', 
    7, 
    'Kizito.Kyando@nmbtz.com', 
    'Kizito', 
    'Kyando', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Kizito.Kyando@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Kizito.Kyando@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Kizito.Kyando@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Kizito.Kyando')
);

-- Edson Kennedy
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Edson.Kennedy', 
    '', 
    7, 
    'Edson.Kennedy@nmbtz.com', 
    'Edson', 
    'Kennedy', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Edson.Kennedy@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Edson.Kennedy@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Edson.Kennedy@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Edson.Kennedy')
);

-- Elipokea Shio
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Elipokea.Shio', 
    '', 
    7, 
    'Elipokea.Shio@nmbtz.com', 
    'Elipokea', 
    'Shio', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Elipokea.Shio@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Elipokea.Shio@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Elipokea.Shio@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Elipokea.Shio')
);

-- Daniel Mwangi
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Daniel.Mwangi', 
    '', 
    7, 
    'Daniel.Mwangi@nmbtz.com', 
    'Daniel', 
    'Mwangi', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Daniel.Mwangi@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Daniel.Mwangi@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Daniel.Mwangi@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Daniel.Mwangi')
);

-- Johanitha Ishemoi
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Johanitha.Ishemoi', 
    '', 
    7, 
    'Johanitha.Ishemoi@nmbtz.com', 
    'Johanitha', 
    'Ishemoi', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Johanitha.Ishemoi@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Johanitha.Ishemoi@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Johanitha.Ishemoi@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Johanitha.Ishemoi')
);

-- Rukia Reli
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Rukia.Reli', 
    '', 
    7, 
    'Rukia.Reli@nmbtz.com', 
    'Rukia', 
    'Reli', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Rukia.Reli@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Rukia.Reli@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Rukia.Reli@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Rukia.Reli')
);

-- Leonard Makungu
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Leonard.Makungu', 
    '', 
    7, 
    'Leonard.Makungu@nmbtz.com', 
    'Leonard', 
    'Makungu', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Leonard.Makungu@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Leonard.Makungu@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Leonard.Makungu@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Leonard.Makungu')
);

-- Rita Lyamuya
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Rita.Lyamuya', 
    '', 
    7, 
    'Rita.Lyamuya@nmbtz.com', 
    'Rita', 
    'Lyamuya', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Rita.Lyamuya@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Rita.Lyamuya@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Rita.Lyamuya@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Rita.Lyamuya')
);

-- Darius Elias
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Darius.Elias', 
    '', 
    7, 
    'Darius.Elias@nmbtz.com', 
    'Darius', 
    'Elias', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Darius.Elias@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Darius.Elias@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Darius.Elias@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Darius.Elias')
);

-- Pauline Mohele
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Pauline.Mohele', 
    '', 
    7, 
    'Pauline.Mohele@nmbtz.com', 
    'Pauline', 
    'Mohele', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Pauline.Mohele@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Pauline.Mohele@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Pauline.Mohele@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Pauline.Mohele')
);

-- Omary Kiliamali
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Omary.Kiliamali', 
    '', 
    7, 
    'Omary.Kiliamali@nmbtz.com', 
    'Omary', 
    'Kiliamali', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Omary.Kiliamali@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Omary.Kiliamali@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Omary.Kiliamali@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Omary.Kiliamali')
);

-- Berry Nsanya
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Berry.Nsanya', 
    '', 
    7, 
    'Berry.Nsanya@nmbtz.com', 
    'Berry', 
    'Nsanya', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Berry.Nsanya@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Berry.Nsanya@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Berry.Nsanya@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Berry.Nsanya')
);

-- Namala Kachecheba
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Namala.Kachecheba', 
    '', 
    7, 
    'Namala.Kachecheba@nmbtz.com', 
    'Namala', 
    'Kachecheba', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Namala.Kachecheba@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Namala.Kachecheba@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Namala.Kachecheba@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Namala.Kachecheba')
);

-- Edmund Njoroge
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Edmund.Njoroge', 
    '', 
    7, 
    'Edmund.Njoroge@nmbtz.com', 
    'Edmund', 
    'Njoroge', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Edmund.Njoroge@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Edmund.Njoroge@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Edmund.Njoroge@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Edmund.Njoroge')
);

-- Subramanian MG
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Subramanian.MG', 
    '', 
    7, 
    'Subramanian.MG@nmbtz.com', 
    'Subramanian', 
    'MG', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Subramanian.MG@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Subramanian.MG@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Subramanian.MG@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Subramanian.MG')
);

-- Maka Mwamwaja
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Maka.Mwamwaja', 
    '', 
    7, 
    'Maka.Mwamwaja@nmbtz.com', 
    'Maka', 
    'Mwamwaja', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Maka.Mwamwaja@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Maka.Mwamwaja@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Maka.Mwamwaja@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Maka.Mwamwaja')
);

-- Calvin Karumuna
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Calvin.Karumuna', 
    '', 
    7, 
    'Calvin.Karumuna@nmbtz.com', 
    'Calvin', 
    'Karumuna', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Calvin.Karumuna@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Calvin.Karumuna@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Calvin.Karumuna@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Calvin.Karumuna')
);

-- Innocent Mosha
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Innocent.Mosha', 
    '', 
    7, 
    'Innocent.Mosha@nmbtz.com', 
    'Innocent', 
    'Mosha', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Innocent.Mosha@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Innocent.Mosha@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Innocent.Mosha@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Innocent.Mosha')
);

-- Elizabeth Mkumbo
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Elizabeth.Mkumbo', 
    '', 
    7, 
    'Elizabeth.Mkumbo@nmbtz.com', 
    'Elizabeth', 
    'Mkumbo', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Elizabeth.Mkumbo@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Elizabeth.Mkumbo@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Elizabeth.Mkumbo@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Elizabeth.Mkumbo')
);

-- Agape Mwakisale
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Agape.Mwakisale', 
    '', 
    7, 
    'Agape.Mwakisale@nmbtz.com', 
    'Agape', 
    'Mwakisale', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Agape.Mwakisale@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Agape.Mwakisale@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Agape.Mwakisale@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Agape.Mwakisale')
);

-- Happy-Monalisa Ngonya
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Happy-Monalisa.Ngonya', 
    '', 
    7, 
    'Happy-Monalisa.Ngonya@nmbtz.com', 
    'Happy-Monalisa', 
    'Ngonya', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Happy-Monalisa.Ngonya@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Happy-Monalisa.Ngonya@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Happy-Monalisa.Ngonya@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Happy-Monalisa.Ngonya')
);

-- Mwaimu Mtingele
INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)
SELECT 
    'Mwaimu.Mtingele', 
    '', 
    7, 
    'Mwaimu.Mtingele@nmbtz.com', 
    'Mwaimu', 
    'Mtingele', 
    'en_GB', 
    NULL, 
    1, 
    NULL, 
    MD5(CONCAT('Mwaimu.Mtingele@nmbtz.com', NOW(), RAND())), 
    'LDAP', 
    NOW(), 
    NULL
WHERE NOT EXISTS (
    SELECT 1 FROM users 
    WHERE 
        LOWER(email) = LOWER('Mwaimu.Mtingele@nmbtz.com')
        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('Mwaimu.Mtingele@nmbtz.com', '@nmbtz.com', '@nmbbank.co.tz'))
        OR LOWER(login) = LOWER('Mwaimu.Mtingele')
);

