show tables;

create table 費目(
    id int not null auto_increment,
    費目名 varchar(255) not null,
    入出力区分 enum('入金', '出金') not null,
    メモ varchar(255),
    primary key(id)
);

alter table 費目 change 入出力区分 入出金区分 enum('入金', '出金') not null;

desc 費目;

INSERT INTO 費目(費目名, 入出金区分)
VALUES
('消費', '出金'),
('給料', '入金'),
('教養娯楽費', '出金'),
('交際費', '出金'),
('水道光熱費', '出金'),
('通信費', '出金'),
('居住費', '出金');

select * from 費目;

update 費目
set 費目名 = '食費'
where id = 1;