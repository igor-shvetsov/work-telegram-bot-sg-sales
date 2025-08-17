- mysql phpmyadmin как быстро развернуть базу данных в которой есть таблица tg-sales-bot-orders c двумя полями id и name имя продукта

CREATE TABLE `tg-sales-bot-orders` (
`id` INT NOT NULL AUTO_INCREMENT,
`name` VARCHAR(255) NOT NULL COMMENT 'Название продукта',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

- таблица должна содержать поле дату создания записи, которое устанавливается самостоятельно и потом может быть считано в php и отображено с учетом разных часовых поясов

https://chat.deepseek.com/a/chat/s/05d0b949-5909-4d30-99cb-21d5a55a066b