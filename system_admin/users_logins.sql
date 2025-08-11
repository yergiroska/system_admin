create table system_admin.users_logins
(
    id               bigint unsigned auto_increment
        primary key,
    user_id          bigint unsigned not null,
    start_connection datetime        null,
    end_connection   datetime        null,
    created_at       timestamp       null,
    updated_at       timestamp       null,
    constraint users_logins_user_id_foreign
        foreign key (user_id) references system_admin.users (id)
            on delete cascade
)
    engine = InnoDB
    collate = utf8mb4_unicode_ci;

