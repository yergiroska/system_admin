create table system_admin.products
(
    id          bigint unsigned auto_increment
        primary key,
    name        varchar(255) not null,
    description varchar(255) not null,
    created_at  timestamp    null,
    updated_at  timestamp    null,
    deleted_at  timestamp    null
)
    engine = InnoDB
    collate = utf8mb4_unicode_ci;

