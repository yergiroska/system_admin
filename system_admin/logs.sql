create table system_admin.logs
(
    id         bigint unsigned auto_increment
        primary key,
    user_id    bigint unsigned not null,
    action     varchar(255)    not null,
    objeto     varchar(255)    null,
    objeto_id  int             null,
    detail     text            not null,
    ip         varchar(255)    not null,
    created_at timestamp       null,
    updated_at timestamp       null,
    constraint logs_user_id_foreign
        foreign key (user_id) references system_admin.users (id)
            on delete cascade
)
    engine = InnoDB
    collate = utf8mb4_unicode_ci;

