DROP TABLE IF EXISTS xxxxx_myaddress_zipcode;
CREATE TABLE xxxxx_myaddress_zipcode (
    code    VARCHAR(5)   DEFAULT ''  NOT NULL,
    oldzip  VARCHAR(5)   DEFAULT ''  NOT NULL,
    zipcode VARCHAR(7)   DEFAULT ''  NOT NULL,
    addr1   VARCHAR(100) DEFAULT ''  NOT NULL,
    addr2   VARCHAR(100) DEFAULT ''  NOT NULL,
    addr3   VARCHAR(100) DEFAULT ''  NOT NULL,
    pref    VARCHAR(100) DEFAULT ''  NOT NULL,
    city    VARCHAR(100) DEFAULT ''  NOT NULL,
    town    VARCHAR(255) DEFAULT ''  NOT NULL,
    d10     TINYINT(4)   DEFAULT '0' NOT NULL,
    d11     TINYINT(4)   DEFAULT '0' NOT NULL,
    d12     TINYINT(4)   DEFAULT '0' NOT NULL,
    d13     TINYINT(4)   DEFAULT '0' NOT NULL,
    d14     TINYINT(4)   DEFAULT '0' NOT NULL,
    d15     TINYINT(4)   DEFAULT '0' NOT NULL,
    KEY zipcode_idx (zipcode),
    KEY pref_idx (pref),
    KEY city_idx (city, town),
    KEY all_idx (pref, city, town)
)
    ENGINE = ISAM;
