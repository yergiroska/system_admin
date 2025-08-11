create table system_admin.password_reset_tokens
(
    email      varchar(255) not null
        primary key,
    token      varchar(255) not null,
    created_at timestamp    null
)
    engine = InnoDB
    collate = utf8mb4_unicode_ci;

