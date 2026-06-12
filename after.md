## Правило зависимостей

```text
Infrastructure -> Application -> Domain
```

Все стрелки направлены внутрь, к домену. Domain не знает ни про Yii, ни про ActiveRecord, ни про HTTP/файлы/очередь/SMSPilot, ни про глобальный `Yii::$app`.

## Выводы
1. Соответствует чистой архитектуре: каталоги разбиты по слоям `Domain` / `Application` / `Infrastructure`, а зависимости направлены внутрь (`Infrastructure → Application → Domain`).
2. Модели больше не связаны с БД через ActiveRecord: доменные сущности `Author`, `Book`, `Subscription`, `BookAuthor` — это чистый PHP с value objects, а ActiveRecord-классы (`BookRecord`, `AuthorRecord`, …) живут в `Infrastructure` и в домен не протекают.
3. Сущности используются только как домен; доступ к БД спрятан за интерфейсами репозиториев и запросов в `Domain` (`RepositoryInterface`, `QueryInterface`). `Application` зависит лишь от интерфейсов, реализации подключаются через DI-контейнер.
4. `SubscriptionForm` теперь занимается только валидацией формата телефона; проверка существования автора и уникальности подписки переехала в `SubscribeToAuthorHandler` и выполняется через `AuthorRepositoryInterface`/`SubscriptionRepositoryInterface` — явные зависимости вместо скрытого обращения к AR.
5. Слабая связанность позволяет unit-тестам полностью изолировать БД.
6. `BookForm` больше не сохраняет файлы — он только валидирует загруженный файл. Сохранение вынесено в интерфейс `FileStorageInterface` (реализация `LocalFileStorage`), имя файла — это VO `PhotoName`, а URL для представления строит `PhotoUrlGenerator` в `Infrastructure`.
7. Инфраструктура не протекает в `Application`: транзакции спрятаны за `TransactionManagerInterface`, а реализация `YiiTransactionManager` с `Yii::$app->db` находится в `Infrastructure`.
8. Ответственности джобы разделены: `SendNewBookSmsJob` лишь делегирует в `SendNewBookSmsHandler`, где поиск телефонов делает репозиторий, а отправку — `SmsGatewayInterface`.
9. DI полный и явный: зависимости приходят через конструкторы (`final readonly` классы), а глобальный `Yii::$app` и статические вызовы остались только в инфраструктурных адаптерах (`YiiTransactionManager`, `YiiNewBookNotifier`, `SendNewBookSmsJob`).
10. Чтение и запись разделены (CQS-стиль): команды идут через репозитории и доменные сущности, а выборки/отчёты — через отдельные `Query`-интерфейсы и read-модели (`BookView`, `TopAuthorRow`).


# Новая архитектура: UML в формате Mermaid

```mermaid
classDiagram
    namespace Controllers {
        class AuthorController
        class BookController
        class SubscriptionController
        class ReportController
        class SiteController
    }

    namespace Forms {
        class AuthorForm
        class AuthorSearchForm
        class BookForm
        class BookSearchForm
        class SubscriptionForm
        class TopAuthorsReportForm
        class LoginForm
    }

    namespace HttpAdapter {
        class UploadedFileDataFactory
    }

    namespace AuthorActions {
        class AuthorIndexAction
        class AuthorCreateAction
        class AuthorViewAction
        class AuthorUpdateAction
        class AuthorDeleteAction
    }

    namespace BookActions {
        class BookIndexAction
        class BookCreateAction
        class BookViewAction
        class BookUpdateAction
        class BookDeleteAction
    }

    namespace OtherActions {
        class SubscriptionIndexAction
        class TopAuthorsAction
        class LoginAction
        class LogoutAction
    }

    namespace CommandHandlers {
        class CreateAuthorHandler
        class UpdateAuthorHandler
        class DeleteAuthorHandler
        class CreateBookHandler
        class UpdateBookHandler
        class DeleteBookHandler
        class SubscribeToAuthorHandler
        class SendNewBookSmsHandler
    }

    namespace QueryHandlers {
        class GetAuthorHandler
        class SearchAuthorsHandler
        class GetBookHandler
        class SearchBooksHandler
        class TopAuthorsHandler
    }

    namespace ReadModels {
        class AuthorView
        class BookView
        class PaginatedResult
        class TopAuthorRow
    }

    namespace ApplicationDTO {
        class SmsMessage
        class UploadedFileData
    }

    namespace DomainEntity {
        class Author
        class Book
        class Subscription
        class BookAuthor
    }

    namespace DomainValueObject {
        class Id
        class AuthorName
        class BookTitle
        class BookYear
        class Isbn
        class PhotoName
        class PhoneNumber
    }

    namespace DomainPorts {
        class AuthorRepositoryInterface {
            <<interface>>
        }
        class BookRepositoryInterface {
            <<interface>>
        }
        class BookAuthorRepositoryInterface {
            <<interface>>
        }
        class SubscriptionRepositoryInterface {
            <<interface>>
        }
        class AuthorQueryInterface {
            <<interface>>
        }
        class BookQueryInterface {
            <<interface>>
        }
        class ReportQueryInterface {
            <<interface>>
        }
        class TransactionManagerInterface {
            <<interface>>
        }
        class FileStorageInterface {
            <<interface>>
        }
        class PhotoUrlGeneratorInterface {
            <<interface>>
        }
        class NewBookNotifierInterface {
            <<interface>>
        }
        class SmsGatewayInterface {
            <<interface>>
        }
    }

    namespace InfrastructurePersistence {
        class AuthorRecord
        class BookRecord
        class BookAuthorRecord
        class SubscriptionRecord
        class User
        class AuthorMapper
        class BookMapper
        class SubscriptionMapper
        class AuthorRepository
        class BookRepository
        class BookAuthorRepository
        class SubscriptionRepository
        class AuthorQuery
        class BookQuery
        class ReportQuery
    }

    namespace InfrastructureGateway {
        class YiiTransactionManager
        class LocalFileStorage
        class RandomFileNameGenerator
        class WebPhotoUrlGenerator
        class YiiNewBookNotifier
        class SmsPilotGateway
    }

    namespace InfrastructureQueue {
        class SendNewBookSmsJob
    }

    %% --- Domain: сущности и value object'ы ---
    Author *-- AuthorName
    Book *-- BookTitle
    Book *-- BookYear
    Book *-- Isbn
    Book *-- PhotoName
    Subscription *-- PhoneNumber
    Subscription *-- Id

    %% --- HTTP: контроллеры → actions → handlers, формы только валидируют ввод ---
    AuthorController ..> AuthorIndexAction
    AuthorController ..> AuthorCreateAction
    AuthorController ..> AuthorViewAction
    AuthorController ..> AuthorUpdateAction
    AuthorController ..> AuthorDeleteAction

    BookController ..> BookIndexAction
    BookController ..> BookCreateAction
    BookController ..> BookViewAction
    BookController ..> BookUpdateAction
    BookController ..> BookDeleteAction

    SubscriptionController ..> SubscriptionIndexAction
    ReportController ..> TopAuthorsAction
    SiteController ..> LoginAction
    SiteController ..> LogoutAction

    AuthorIndexAction ..> AuthorSearchForm
    AuthorIndexAction --> SearchAuthorsHandler
    AuthorCreateAction ..> AuthorForm
    AuthorCreateAction --> CreateAuthorHandler
    AuthorViewAction --> GetAuthorHandler
    AuthorUpdateAction ..> AuthorForm
    AuthorUpdateAction --> UpdateAuthorHandler
    AuthorUpdateAction --> GetAuthorHandler
    AuthorDeleteAction --> DeleteAuthorHandler

    BookIndexAction ..> BookSearchForm
    BookIndexAction --> SearchBooksHandler
    BookIndexAction --> AuthorRepositoryInterface
    BookCreateAction ..> BookForm
    BookCreateAction --> CreateBookHandler
    BookCreateAction --> AuthorRepositoryInterface
    BookCreateAction ..> UploadedFileDataFactory
    BookViewAction --> GetBookHandler
    BookUpdateAction ..> BookForm
    BookUpdateAction --> UpdateBookHandler
    BookUpdateAction --> GetBookHandler
    BookUpdateAction --> AuthorRepositoryInterface
    BookUpdateAction ..> UploadedFileDataFactory
    BookDeleteAction --> DeleteBookHandler

    SubscriptionIndexAction ..> SubscriptionForm
    SubscriptionIndexAction --> SubscribeToAuthorHandler
    SubscriptionIndexAction --> AuthorRepositoryInterface

    TopAuthorsAction ..> TopAuthorsReportForm
    TopAuthorsAction --> TopAuthorsHandler

    SiteController ..> LoginForm
    UploadedFileDataFactory ..> UploadedFileData

    %% --- Command handlers → порты и домен ---
    CreateAuthorHandler --> AuthorRepositoryInterface
    CreateAuthorHandler --> Author
    UpdateAuthorHandler --> AuthorRepositoryInterface
    UpdateAuthorHandler --> Author
    DeleteAuthorHandler --> AuthorRepositoryInterface

    CreateBookHandler --> BookRepositoryInterface
    CreateBookHandler --> BookAuthorRepositoryInterface
    CreateBookHandler --> FileStorageInterface
    CreateBookHandler --> NewBookNotifierInterface
    CreateBookHandler --> TransactionManagerInterface
    CreateBookHandler --> Book
    CreateBookHandler --> BookAuthor

    UpdateBookHandler --> BookRepositoryInterface
    UpdateBookHandler --> BookAuthorRepositoryInterface
    UpdateBookHandler --> FileStorageInterface
    UpdateBookHandler --> NewBookNotifierInterface
    UpdateBookHandler --> TransactionManagerInterface

    DeleteBookHandler --> BookRepositoryInterface
    DeleteBookHandler --> BookAuthorRepositoryInterface
    DeleteBookHandler --> FileStorageInterface
    DeleteBookHandler --> TransactionManagerInterface

    SubscribeToAuthorHandler --> AuthorRepositoryInterface
    SubscribeToAuthorHandler --> SubscriptionRepositoryInterface
    SubscribeToAuthorHandler --> Subscription

    SendNewBookSmsHandler --> AuthorRepositoryInterface
    SendNewBookSmsHandler --> BookRepositoryInterface
    SendNewBookSmsHandler --> SubscriptionRepositoryInterface
    SendNewBookSmsHandler --> SmsGatewayInterface
    SendNewBookSmsHandler ..> SmsMessage

    %% --- Query handlers → query-порты и read-модели ---
    GetAuthorHandler --> AuthorQueryInterface
    GetAuthorHandler ..> AuthorView
    SearchAuthorsHandler --> AuthorQueryInterface
    SearchAuthorsHandler ..> PaginatedResult

    GetBookHandler --> BookQueryInterface
    GetBookHandler ..> BookView
    SearchBooksHandler --> BookQueryInterface
    SearchBooksHandler ..> PaginatedResult

    TopAuthorsHandler --> ReportQueryInterface
    TopAuthorsHandler ..> TopAuthorRow

    %% --- Infrastructure реализует порты ---
    AuthorRepository ..|> AuthorRepositoryInterface
    BookRepository ..|> BookRepositoryInterface
    BookAuthorRepository ..|> BookAuthorRepositoryInterface
    SubscriptionRepository ..|> SubscriptionRepositoryInterface

    AuthorQuery ..|> AuthorQueryInterface
    BookQuery ..|> BookQueryInterface
    ReportQuery ..|> ReportQueryInterface

    YiiTransactionManager ..|> TransactionManagerInterface
    LocalFileStorage ..|> FileStorageInterface
    WebPhotoUrlGenerator ..|> PhotoUrlGeneratorInterface
    YiiNewBookNotifier ..|> NewBookNotifierInterface
    SmsPilotGateway ..|> SmsGatewayInterface

    %% --- Persistence: ActiveRecord изолирован за mapper/repository/query ---
    AuthorRepository --> AuthorMapper
    AuthorMapper --> AuthorRecord
    AuthorMapper --> Author

    BookRepository --> BookMapper
    BookMapper --> BookRecord
    BookMapper --> Book

    SubscriptionRepository --> SubscriptionMapper
    SubscriptionMapper --> SubscriptionRecord
    SubscriptionMapper --> Subscription

    BookAuthorRepository --> BookAuthorRecord
    BookAuthorRepository --> BookAuthor

    AuthorQuery --> AuthorRecord
    AuthorQuery ..> AuthorView

    BookQuery --> BookRecord
    BookQuery --> BookAuthorRecord
    BookQuery --> PhotoUrlGeneratorInterface
    BookQuery ..> BookView

    ReportQuery ..> TopAuthorRow

    %% --- Gateway и очередь ---
    LocalFileStorage --> RandomFileNameGenerator
    LocalFileStorage ..> UploadedFileData
    LocalFileStorage --> PhotoName

    YiiNewBookNotifier --> SendNewBookSmsJob
    SendNewBookSmsJob ..> SendNewBookSmsHandler
```
