# Installation

In dieser Datei wird die Installation erläutert.

## Bundle zu Dependencies hinzufügen

Das Bundle muss zunächst manuell in die composer.json-Datei eingebunden
werden. Dazu wird die "require"-Liste ergänzt und "repositories" hinzugefügt. 

```json
    ...
    "require": {
        ...
        "kagonlineteam/ldap-bundle": "dev-master"
    },
    "repositories": [
        {
            "type": "vcs",
            "url":  "https://github.com/kagonlineteam/ldap-bundle.git"
        }
    ],
    ...
```

Im Anschluss soll Composer nun die Änderungen übernehmen:

```console
$ composer update
```

In config/bundles.php sollte nun auch das LdapBundle stehen.

## Konfiguration ergänzen

Damit das Bundle im Symfony Ökosystem konfiguriert werden kann, wird die Datei config/packages/kagonlineteam_ldap.yaml mit dem gegebenen Inhalt erstellt:

```yaml
kagonlineteam_ldap:
    ldap_url: '%env(LDAP_URL)%'
    ldap_bind: '%env(LDAP_BIND)%'
    base_dn: 
```

Die Ldap URL und die Bind Credentials werden aus den Environment Variablen gelesen. Die Base DN wird in dieser Datei konfiguriert.
Die Environment Variablen können in diesem Format in die .env.* Dateien eingefügt werden:

```txt
###> kagonlineteam/ldap-bundle ###
# Format described at https://ldap.com/ldap-urls/
LDAP_URL="ldap://example.com:389"
LDAP_BIND="bind_dn?bind_password"
###< kagonlineteam/ldap-bundle ###
```