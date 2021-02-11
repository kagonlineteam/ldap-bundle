# Benutzung

Nachdem nun alles eingerichtet worden ist, kann endlich mit den Objekten gearbeitet werden.

## Queries

Jede Repository, die die `AbstractRepository` ableitet, verfügt über zwei grundlegende Queries:

```php
<?php

namespace App\Acme;

use App\Repository\UserRepository;

class SomeService 
{
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function query(): void
    {
        // Ein Objekt über eine DN erhalten.
        $user = $this->repository->find('cn=Mueller,ou=users');

        // Objekte zu allen passenden Einträgen in der konfigurierten Base DN finden.
        // $users ist hierbei vom allgemeinen Typ "iterable", ist in Wirklichkeit aber 
        // ein Generator: https://www.php.net/manual/en/language.generators.overview.php.
        $users = $this->repository->findAll();
    }
}
```

Weiter Query-Methoden können selbst in der Repository geschrieben werden. Dazu wird der 
[QueryBuilder](https://github.com/kagonlineteam/ldap-bundle/blob/master/docs/04-query-builder.md) 
benutzt.

## Auf den Ldap schreiben

Natürlich können auch neue Objekte auf dem Ldap gespeichert werden. Der Funktionsaufruf `persist`
markiert das Objekt lediglich und speichert es dann beim nächsten `commit`-Aufruf ab.

```php
    //...
    public function add(): void
    {
        $user = new User();

        $this->repository->persist($user);
        $this->repository->commit();
    }
```

Das Löschen eines Eintrages erfolgt analog.

```php
    //...
    public function remove(): void
    {
        $user = $this->repository->find('cn=Mueller,ou=users');

        $this->repository->remove($user);
        $this->repository->commit();
    }
```

Falls sich ein Objekt verändern sollte, wird der Ldap Eintrag beim nächsten `commit`-Aufruf
automatisch aktualisiert.

```php
    //...
    public function edit(): void
    {
        $user = $this->repository->find('cn=Mueller,ou=users');

        $user->setFirstName('Peter');

        $this->repository->commit();
    }
```

## Bind Operation

Eine Bind Operation kann auch ausgeführt werden. Die Vorgehensweise unterscheidet sich aber deutlich von der bisherigen Nutzung.

```php
<?php

namespace App\Acme;

use KAGOnlineTeam\LdapBundle\ManagerInterface;
use KAGOnlineTeam\LdapBundle\Request\BindRequest;
use KAGOnlineTeam\LdapBundle\Response\FailureResponse;

class SomeService 
{
    private $manager;

    public function __construct(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function verify(string $dn, string $password): void
    {
        $reponse = $this->manager->query(
            new BindRequest($dn, $password)
        );

        if ($response instanceof FailureResponse) {
            throw new \Exception($reponse->getMessage());
        }
    }
}
```