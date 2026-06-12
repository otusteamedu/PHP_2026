## Выводы
1. Не соответствует слоистой архитектуре, другое распределение папок и ответственностей.
2. Модели связаны с бд за счет паттерна ActiveRecord.
3. Модели используются в сервисах и как Domain-сущности и как доступ к БД.
4. SubscriptionForm для валидации данных использует модель AR для доступа к БД, при этом не имеет явной зависимости.
5. Unit тесты при такой сильной связанности кода с БД, не позволяют изолировать БД.
6. BookForm помимо того, что занимается валидацией данных еще занимается сохранением файлов.
7. Infrastructure протекает в Application: TransactionManager использует Yii::$app→db, а BookService напрямую зависит от него.
8. Job смешивает ответственности - и находит подписчиков, и отправляет всем им смс.
9. DI частичный, внутри классов есть статические вызовы и глобальный Yii::$app.


# Исходная архитектура: UML в формате Mermaid

```mermaid
classDiagram
    namespace Controllers {
        class AuthorController
        class BookController
        class SubscriptionController
        class ReportController
    }

    namespace Forms {
        class AuthorForm
        class BookForm
        class SubscriptionForm
        class AuthorSearchForm
        class BookSearchForm
        class TopAuthorsReportSearchForm
    }

    namespace Services {
        class CrudService {
            <<abstract>>
        }
        class AuthorService
        class BookService
        class SubscriptionService
        class TransactionManager
    }

    namespace DTO {
        class AuthorDto
        class BookDto
        class SubscriptionDto
    }

    namespace DomainModels {
        class Author
        class Book
        class Subscription
        class BookAuthor
    }

    namespace Jobs {

        class SendNewBookSmsJob
    }

    namespace Behaviors {
        class NotifySubscribersBehavior

    }

    CrudService <|-- AuthorService
    CrudService <|-- BookService

    AuthorController --> AuthorService
    BookController --> BookService
    SubscriptionController --> SubscriptionService

    AuthorController ..> AuthorSearchForm
    AuthorController ..> AuthorForm
    BookController ..> BookSearchForm
    BookController ..> BookForm
    SubscriptionController ..> SubscriptionForm
    ReportController ..> TopAuthorsReportSearchForm

    AuthorService --> AuthorForm
    BookService --> BookForm
    SubscriptionService --> SubscriptionForm

    AuthorForm --> AuthorDto
    BookForm --> BookDto
    SubscriptionForm --> SubscriptionDto

    AuthorService --> Author
    BookService --> Book
    BookService --> BookAuthor
    SubscriptionService --> Subscription
    BookService --> TransactionManager

    BookAuthor ..> NotifySubscribersBehavior
    NotifySubscribersBehavior --> SendNewBookSmsJob
    SendNewBookSmsJob --> Author
    SendNewBookSmsJob --> Book
    SendNewBookSmsJob --> Subscription
```
