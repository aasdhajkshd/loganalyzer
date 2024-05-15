DO $$ BEGIN
    IF EXISTS (SELECT 1 FROM pg_roles WHERE rolname = 'rsyslog') THEN
        RAISE NOTICE 'Роль существует.';	
    ELSE
        CREATE ROLE rsyslog WITH LOGIN;
		ALTER USER rsyslog WITH PASSWORD 'password';
        RAISE NOTICE 'Роль rsyslog не существует.';
    END IF;
END $$;
\dg
DROP DATABASE IF EXISTS rsyslog;
CREATE DATABASE rsyslog OWNER rsyslog LOCALE 'en_US.utf8' ENCODING 'SQL_ASCII' TEMPLATE template0;

\l
-- GRANT CONNECT, CREATE, USAGE ON DATABASE syslog TO rsyslog;
GRANT ALL PRIVILEGES ON DATABASE rsyslog TO rsyslog;
\c rsyslog rsyslog
CREATE TABLE systemevents
(
    ID serial not null primary key,
    CustomerID bigint,
    ReceivedAt timestamp without time zone NULL,
    DeviceReportedTime timestamp without time zone NULL,
    Facility smallint NULL,
    Priority smallint NULL,
    FromHost varchar(60) NULL,
    Message text,
    NTSeverity int NULL,
    Importance int NULL,
    EventSource varchar(60),
    EventUser varchar(60) NULL,
    EventCategory int NULL,
    EventID int NULL,
    EventBinaryData text NULL,
    MaxAvailable int NULL,
    CurrUsage int NULL,
    MinUsage int NULL,
    MaxUsage int NULL,
    InfoUnitID int NULL ,
    SysLogTag varchar(60),
	ProcessID varchar(60) NULL,
    EventLogType varchar(60),
    GenericFileName VarChar(60),
    SystemID int NULL,
    Checksum varchar(60) NULL
);

CREATE TABLE systemeventsproperties
(
    ID serial not null primary key,
    SystemEventID int NULL ,
    ParamName varchar(255) NULL ,
    ParamValue text NULL
);
\dt
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO rsyslog;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO rsyslog;
-- GRANT ALL ON ALL FUNCTIONS IN SCHEMA public to rsyslog;
-- ALTER DATABASE syslog OWNER TO rsyslog;
\z 
\c rsyslog rsyslog
SELECT MAX(id) FROM systemevents;
