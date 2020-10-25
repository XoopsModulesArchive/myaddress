# phpMyAdmin SQL Dump
# version 2.5.2
# http://www.phpmyadmin.net
#

#
# Table structure for table `myaddress_cat`
#

CREATE TABLE myaddress_cat (
    cid    INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    pid    INT(10) UNSIGNED NOT NULL DEFAULT '0',
    title  VARCHAR(50)      NOT NULL DEFAULT '',
    imgurl VARCHAR(150)     NOT NULL DEFAULT '',
    PRIMARY KEY (cid),
    KEY pid (pid)
)
    ENGINE = ISAM;

#
# Table structure for table `myaddress_addressbook`
#

CREATE TABLE myaddress_addressbook (
    aid           INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
    cid           TINYINT(4) UNSIGNED NOT NULL DEFAULT '0',
    relations     VARCHAR(50)         NOT NULL DEFAULT '',
    first_name    VARCHAR(50)         NOT NULL DEFAULT '',
    last_name     VARCHAR(50)         NOT NULL DEFAULT '',
    fullname      VARCHAR(100)        NOT NULL DEFAULT '',
    first_name_jh VARCHAR(50)         NOT NULL DEFAULT '',
    last_name_jh  VARCHAR(50)         NOT NULL DEFAULT '',
    fullname_jh   VARCHAR(100)        NOT NULL DEFAULT '',
    first_name2   VARCHAR(50)         NOT NULL DEFAULT '',
    myzipcode     VARCHAR(20)         NOT NULL DEFAULT '',
    myaddress1    VARCHAR(100)        NOT NULL DEFAULT '',
    myaddress2    VARCHAR(100)        NOT NULL DEFAULT '',
    myaddress3    VARCHAR(100)        NOT NULL DEFAULT '',
    myphone       VARCHAR(20)         NOT NULL DEFAULT '',
    myfax         VARCHAR(20)         NOT NULL DEFAULT '',
    mycellphone1  VARCHAR(20)         NOT NULL DEFAULT '',
    mycellphone2  VARCHAR(20)         NOT NULL DEFAULT '',
    myemail1      VARCHAR(50)         NOT NULL DEFAULT '',
    myemail2      VARCHAR(50)         NOT NULL DEFAULT '',
    myemail3      VARCHAR(50)         NOT NULL DEFAULT '',
    myemail4      VARCHAR(50)         NOT NULL DEFAULT '',
    myweb         VARCHAR(100)        NOT NULL DEFAULT '',
    mycomments    TEXT                NOT NULL,
    c_id          INT(10)             NOT NULL DEFAULT '0',
    cdepart       VARCHAR(100)        NOT NULL DEFAULT '',
    ctitle        VARCHAR(100)        NOT NULL DEFAULT '',
    cphone        VARCHAR(20)         NOT NULL DEFAULT '',
    cfax          VARCHAR(20)         NOT NULL DEFAULT '',
    disclosed     TINYINT(1)          NOT NULL DEFAULT '1',
    uid           INT(10) UNSIGNED    NOT NULL DEFAULT '0',
    updated       TIMESTAMP(14)       NOT NULL,
    PRIMARY KEY (aid),
    KEY cid (cid),
    KEY classified (relations),
    KEY fullname_jh (fullname_jh),
    KEY c_id (c_id)
)
    ENGINE = ISAM;

#
# Table structure for table `myaddress_company`
#

CREATE TABLE myaddress_company (
    c_id      INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
    cid       TINYINT(4) UNSIGNED NOT NULL DEFAULT '0',
    cname     VARCHAR(100)        NOT NULL DEFAULT '',
    cname_jh  VARCHAR(100)        NOT NULL DEFAULT '',
    cdivision VARCHAR(100)        NOT NULL DEFAULT '',
    czipcode  VARCHAR(20)         NOT NULL DEFAULT '',
    caddress1 VARCHAR(100)        NOT NULL DEFAULT '',
    caddress2 VARCHAR(100)        NOT NULL DEFAULT '',
    caddress3 VARCHAR(100)        NOT NULL DEFAULT '',
    cphone    VARCHAR(20)         NOT NULL DEFAULT '',
    cfax      VARCHAR(20)         NOT NULL DEFAULT '',
    cweb      VARCHAR(100)        NOT NULL DEFAULT '',
    ccomments TEXT                NOT NULL,
    uid       INT(10)             NOT NULL DEFAULT '0',
    updated   TIMESTAMP(14)       NOT NULL,
    UNIQUE KEY ckey (c_id, cdivision),
    KEY cid (cid),
    KEY cname_jh (cname_jh)
)
    ENGINE = ISAM;

#
# Table structure for table `myaddress_relations`
#

CREATE TABLE myaddress_relations (
    rid    TINYINT(4) UNSIGNED  NOT NULL AUTO_INCREMENT,
    title  VARCHAR(50)          NOT NULL DEFAULT '',
    weight TINYINT(4) UNSIGNED  NOT NULL DEFAULT '0',
    gid    SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (rid),
    KEY gid (gid)
)
    ENGINE = ISAM;
