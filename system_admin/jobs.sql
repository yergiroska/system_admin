create table system_admin.jobs
(
    id           bigint unsigned auto_increment
        primary key,
    queue        varchar(255)     not null,
    payload      longtext         not null,
    attempts     tinyint unsigned not null,
    reserved_at  int unsigned     null,
    available_at int unsigned     not null,
    created_at   int unsigned     not null
)
    engine = InnoDB
    collate = utf8mb4_unicode_ci;

create index jobs_queue_index
    on system_admin.jobs (queue);

