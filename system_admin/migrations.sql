create table system_admin.migrations
(
    id        int unsigned auto_increment
        primary key,
    migration varchar(255) not null,
    batch     int          not null
)
    engine = InnoDB
    collate = utf8mb4_unicode_ci;

