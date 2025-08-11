create table system_admin.cache
(
    `key`      varchar(255) not null
        primary key,
    value      mediumtext   not null,
    expiration int          not null
)
    engine = InnoDB
    collate = utf8mb4_unicode_ci;

