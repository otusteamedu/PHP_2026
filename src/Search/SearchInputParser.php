<?php

declare(strict_types=1);

namespace App\Search;

use Docopt;
use Docopt\ExitException;

final class SearchInputParser
{
    private const string DOC = <<<'DOCOPT'
        Поиск по книжному магазину.
        
        Usage:
          bin/search.php [--query=<text>] [--category=<name>] [--min-price=<amount>] [--max-price=<amount>] [--limit=<count>] [--in-stock]
          bin/search.php (-h | --help)
        
        Options:
          -h --help             Показывает эту инструкцию.
          --query=<text>        Поисковой запрос по названию книги.
          --category=<name>     Категория.
          --min-price=<amount>  Минимальная цена.
          --max-price=<amount>  Максимальная цена.
          --limit=<count>       Сколько максимум записей вернуть.
          --in-stock            Вернет только книги в наличии.
        
        Examples:
          php bin/search.php --help
          php bin/search.php --query="рыцОри" --category="Исторический роман" --max-price=2000 --limit=25 --in-stock
    DOCOPT;

    /**
     * @param string[] $argv
     * @throws ExitException
     */
    public function parse(array $argv): SearchInput
    {
        $arguments = Docopt::handle(self::DOC, [
            'argv' => $argv,
            'help' => true,
        ]);

        return SearchInput::create($arguments->args);
    }
}
