create table system_admin.cache_locks
(
    `key`      varchar(255) not null
        primary key,
    owner      varchar(255) not null,
    expiration int          not null
)
    engine = InnoDB
    collate = utf8mb4_unicode_ci;

