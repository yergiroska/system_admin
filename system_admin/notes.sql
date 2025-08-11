create table system_admin.notes
(
    id         bigint unsigned auto_increment
        primary key,
    title      varchar(255)         not null,
    contents   text                 not null,
    completed  tinyint(1) default 0 not null,
    created_at timestamp            null,
    updated_at timestamp            null,
    deleted_at timestamp            null
)
    engine = InnoDB
    collate = utf8mb4_unicode_ci;

