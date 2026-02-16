<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260216093822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Заполнение данными';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
INSERT INTO products (id, title,price) VALUES
	 ('1','Iphone','100'),
	 ('2','Наушники','5000'),
	 ('3','Чехол','10');
SQL
        );

        $this->addSql(<<<'SQL'
INSERT INTO country_taxes (code,amount) VALUES
	 ('de','19'),
	 ('it','22'),
	 ('gr','24'),
	 ('fr','20');
SQL
        );

        $this->addSql(<<<'SQL'
INSERT INTO coupons (code,amount,"type") VALUES
	 ('P10','10','fix'),
	 ('P100','100','percent'),
	 ('P50','5','percent');
SQL
        );

    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
DELETE FROM coupons;
SQL
        );
        $this->addSql(<<<'SQL'
DELETE FROM country_taxes;
SQL
        );
        $this->addSql(<<<'SQL'
DELETE FROM products;
SQL
        );
    }
}
