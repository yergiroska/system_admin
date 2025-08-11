create table system_admin.sessions
(
    id            varchar(255)    not null
        primary key,
    user_id       bigint unsigned null,
    ip_address    varchar(45)     null,
    user_agent    text            null,
    payload       longtext        not null,
    last_activity int             not null
)
    engine = InnoDB
    collate = utf8mb4_unicode_ci;

create index sessions_last_activity_index
    on system_admin.sessions (last_activity);

create index sessions_user_id_index
    on system_admin.sessions (user_id);

