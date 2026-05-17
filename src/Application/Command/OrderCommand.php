<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\Configurator\CheeseBurgerReceipt;
use App\Application\Configurator\CustomReceipt;
use App\Domain\Configurator\ProductConfiguratorInterface;
use App\Application\UseCase\CookProducts\Handler;
use App\Application\UseCase\CookProducts\Order;
use App\Application\UseCase\CookProducts\Position;
use App\Domain\Enum\Ingredient;
use ArrayObject;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'app:order', description: 'Оформить заказ в интерактивном режиме')]
final class OrderCommand extends Command
{
    /** @var array<string, string> */
    private const array PRODUCTS = [
        'Бургер' => 'burger',
        'Хот-дог' => 'hotdog',
        'Сэндвич' => 'sandwich',
    ];

    public function __construct(private readonly Handler $handler)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        if (!$helper instanceof QuestionHelper) {
            throw new RuntimeException('Question helper is not available.');
        }

        $positions = new ArrayObject();
        $addMore = true;

        while ($addMore) {
            $position = $this->askPosition($input, $output, $helper);
            $positions->append($position);

            $addMoreQuestion = new ConfirmationQuestion('Добавить еще позицию? [y/N] ', false);
            $addMore = $helper->ask($input, $output, $addMoreQuestion);
        }

        $response = ($this->handler)(new Order($positions));

        $output->writeln('');
        $output->writeln('<info>Результат заказа:</info>');
        foreach ($response->resultProducts as $index => $resultProduct) {
            $line = $resultProduct === '' ? 'Позиция забракована контролем качества' : $resultProduct;
            $output->writeln(sprintf('%d) %s', $index + 1, $line));
        }

        return Command::SUCCESS;
    }

    private function askPosition(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $helper
    ): Position {
        $productQuestion = new ChoiceQuestion(
            'Выберите продукт:',
            array_keys(self::PRODUCTS)
        );
        $productLabel = $helper->ask($input, $output, $productQuestion);

        $typeQuestion = new ChoiceQuestion(
            'Как оформить позицию?',
            ['По рецепту', 'Кастом']
        );
        $typeLabel = $helper->ask($input, $output, $typeQuestion);

        $configurator = $typeLabel === 'По рецепту'
            ? $this->askRecipeConfigurator($input, $output, $helper)
            : $this->askCustomConfigurator($input, $output, $helper);

        return new Position(self::PRODUCTS[$productLabel], $configurator);
    }

    private function askRecipeConfigurator(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $helper
    ): ProductConfiguratorInterface {
        $recipes = $this->recipes();
        $recipeQuestion = new ChoiceQuestion('Выберите рецепт:', array_keys($recipes));
        $recipeLabel = $helper->ask($input, $output, $recipeQuestion);

        return $recipes[$recipeLabel];
    }

    private function askCustomConfigurator(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $helper
    ): ProductConfiguratorInterface {
        $ingredientsQuestion = new Question(
            'Ингредиенты через запятую (BREAD, CHEESE, KETCHUP, MEAT, VEGETABLES, ONION), Enter - без допов: ',
            ''
        );
        $ingredientsRaw = (string) $helper->ask($input, $output, $ingredientsQuestion);

        $ingredients = $this->parseIngredients($ingredientsRaw);
        return new CustomReceipt(new ArrayObject($ingredients));
    }

    /** @return Ingredient[] */
    private function parseIngredients(string $ingredientsRaw): array
    {
        if ($ingredientsRaw === '') {
            return [];
        }

        $result = [];
        foreach (explode(',', $ingredientsRaw) as $ingredientRaw) {
            $normalized = strtoupper(trim($ingredientRaw));
            if ($normalized === '') {
                continue;
            }

            $ingredient = $this->resolveIngredient($normalized);
            if ($ingredient !== null) {
                $result[] = $ingredient;
            }
        }

        return $result;
    }

    private function resolveIngredient(string $name): ?Ingredient
    {
        return array_find(Ingredient::cases(), fn($case) => $case->name === $name);
    }

    /** @return array<string, ProductConfiguratorInterface> */
    private function recipes(): array
    {
        return [
            'Чизбургер' => new CheeseBurgerReceipt(),
        ];
    }
}
