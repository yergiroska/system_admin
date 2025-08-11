create table system_admin.customers
(
    id                bigint unsigned auto_increment
        primary key,
    first_name        varchar(255) not null,
    last_name         varchar(255) not null,
    birth_date        date         not null,
    identity_document varchar(255) not null,
    created_at        timestamp    null,
    updated_at        timestamp    null,
    deleted_at        timestamp    null,
    constraint customers_identity_document_unique
        unique (identity_document)
)
    engine = InnoDB
    collate = utf8mb4_unicode_ci;

