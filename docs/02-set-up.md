# Einrichtung

Im Folgenden sollen nun die Entity Klasse und die Repository Klasse, erzeugt werden.

## Entity erstellen

Die Entity Klasse stellt die Verbindung zwischen den Ldap Einträgen und den PHP Objekten her und beschreibt mit den Annotations wie ein Objekt im Ldap gespeichert bzw. aus dem Ldap gelesen werden kann.

```php
<?php

namespace App\Entity;

use KAGOnlineTeam\LdapBundle\Annotation as Ldap;

/**
 * @Ldap\Entry(
 *     repositoryClass="App\Repository\UserRepository",
 *     objectClasses={
 *         "inetOrgPerson",
 *         "person",
 *         "top"
 *     }
 * )
 */
class User
{
    /**
     * @Ldap\DistinguishedName(
     *     type="string"
     * )
     */
    private $dn;

    /**
     * @Ldap\Attribute(
     *     description="uid",
     *     type="scalar"
     * )
     */
    private $username;

    /**
     * @Ldap\Attribute(
     *     description="cn",
     *     type="scalar"
     * )
     */
    private $commonName;

    /**
     * @Ldap\Attribute(
     *     description="givenName",
     *     type="array"
     * )
     */
    private $firstName;

    /**
     * @Ldap\Attribute(
     *     description="sn",
     *     type="array"
     * )
     */
    private $lastName;
    
    // Custom methods 
    // ...
}
```

### Entry Annotation

#### `repositoryClass`

Der komplette Klassenname der Repository Klasse inklusive des Namespaces.

#### `objectClasses`

Ein Array aller `objectClass`-Werte, die ein Ldap Eintrag besitzen muss, damit aus diesem ein Objekt dieser Klasse erstellt werden kann.

### DistinguishedName Annotation

#### `type`

Mit dem Wert `string` bleibt die DN einfach ein String. Mit `object` wird anstatt des Strings ein Objekt der Klasse `KAGOnlineTeam\LdapBundle\Attribute\DistinguishedName` erzeugt und der DN Klassenvariablen zugewiesen.

### Attribute Annotation

#### `description`

Das Ldap Attribut für diese Klassenvariable.

#### `type`

`array` Die Werte des Ldap Attributes werden in einem Array gespeichert.

`scalar` Das Ldap Attribut wird hiermit als Single-Value betrachtet und der (erste) Wert wird übergeben.

`multivalue` Hiermit wird ein Objekt der Klasse `KAGOnlineTeam\LdapBundle\Attribute\MultiValue` genutzt.

## Repository erstellen

Die Repository Klasse ist ein Service in Symfony und wird dafür benutzt um Objekte aus einer Query zu laden und 
Objekte im Ldap zu speichern/löschen. Dazu kann einfach die `KAGOnlineTeam\LdapBundle\AbstractRepository` abgeleitet 
werden. Damit sind alle wichtigen Funktionen bereits implementiert.

```php
<?php

namespace App\Repository;

use App\Entity\User;
use KAGOnlineTeam\LdapBundle\AbstractRepository;
use KAGOnlineTeam\LdapBundle\ManagerInterface;
use KAGOnlineTeam\LdapBundle\Query\Options;

class UserRepository extends AbstractRepository
{
    public function __construct(ManagerInterface $manager)
    {
        parent::__construct($manager, User::class);
    }

    public function findByUsername(string $username): iterable
    {
        $query = $this->createQueryBuilder()
            ->filterEquality()
                ->with('username', $username)
            ->end()
            ->scope(Options::SCOPE_SUB)
            ->make();

        return $this->execute($query);
    }
}
```

Der Konstruktor muss, wie im Beispiel, den Konstruktor der Parent Klasse mit dem `ManagerInterface`-Service und 
der Klasse der Entität aufrufen. Denkbar wären im Konstruktor ebenfalls weitere Services, die dann in den eigenen 
Methoden benutzt werden.
